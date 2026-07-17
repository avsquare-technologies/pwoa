<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Business;
use App\Actions\Auth\RegisterUser;
use App\Actions\Membership\SubscribeUser;
use App\Mail\RegisterOtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class RegisterWizard extends Component
{
    // Wizard navigation
    public $currentStep = 1;
    public $totalSteps = 4;

    // Step 1: Account details
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;

    // Step 2: Company details
    public $company_name;
    public $website;
    public $company_phone;
    public $company_email;
    public $address;
    public $country_id;
    public $state_id;
    public $city_id;
    public $zip;
    public $years_in_business;
    public $license_number;
    public $is_insured = false;
    public $directory_type = 'contractor'; // 'contractor' or 'vendor'

    // Step 3: Membership Tier
    public $membership_tier = 'standard'; // 'standard' or 'gold'

    // Step 4: OTP Verification
    public $otp;
    public $otpSent = false;
    public $generatedOtp;
    public $otpExpiresAt;

    // Data for dropdowns
    public $countries = [];
    public $states = [];
    public $cities = [];

    public function mount()
    {
        $this->countries = Country::orderBy('name')->get();
    }

    public function updatedCountryId($value)
    {
        $this->state_id = null;
        $this->city_id = null;
        $this->states = $value ? State::where('country_id', $value)->orderBy('name')->get()->toArray() : [];
        $this->cities = [];
    }

    public function updatedStateId($value)
    {
        $this->city_id = null;
        $this->cities = $value ? City::where('state_id', $value)->orderBy('name')->get()->toArray() : [];
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->currentStep++;

        if ($this->currentStep === 4 && !$this->otpSent) {
            $this->sendOtp();
        }
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    protected function validateStep()
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => ['required', 'string', 'regex:/^[0-9\-\+\(\)\s]{10,20}$/'],
                'password' => 'required|string|min:8|confirmed',
            ], [
                'phone.regex' => 'The phone number must be a valid 10 to 20-digit number (spaces, dashes, plus, and parentheses are allowed).',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'company_name' => 'required|string|min:3|max:255',
                'website' => 'nullable|url|max:255',
                'company_phone' => ['nullable', 'string', 'regex:/^[0-9\-\+\(\)\s]{10,20}$/'],
                'company_email' => 'nullable|email|max:255',
                'address' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'zip' => ['required', 'string', 'regex:/^[a-zA-Z0-9\s\-]{3,10}$/'],
                'years_in_business' => 'nullable|integer|min:0|max:100',
                'license_number' => 'nullable|string|max:100',
                'is_insured' => 'nullable|boolean',
                'directory_type' => 'required|in:contractor,vendor',
            ], [
                'company_phone.regex' => 'The company phone number must be a valid 10 to 20-digit number.',
                'zip.regex' => 'The zip/postal code must be between 3 and 10 alphanumeric characters (spaces and dashes are allowed).',
            ]);
        } elseif ($this->currentStep === 3) {
            $this->validate([
                'membership_tier' => 'required|in:standard,gold',
            ]);
            $this->membership_tier = 'standard';
        }
    }

    public function sendOtp()
    {
        $this->generatedOtp = sprintf("%06d", mt_rand(100000, 999999));
        $this->otpExpiresAt = now()->addMinutes(15);
        $this->otpSent = true;

        Mail::to($this->email)->send(new RegisterOtpMail([
            'name' => $this->first_name . ' ' . $this->last_name,
            'otp' => $this->generatedOtp,
        ]));
    }

    public function resendOtp()
    {
        $this->sendOtp();
        session()->flash('otp_success', 'A new verification code has been sent to your email.');
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!$this->generatedOtp || now()->greaterThan($this->otpExpiresAt)) {
            $this->addError('otp', 'The verification code has expired. Please request a new one.');
            return;
        }

        if ($this->otp !== $this->generatedOtp) {
            $this->addError('otp', 'The verification code is invalid.');
            return;
        }

        // OTP is verified! Create user account & listing.
        $user = app(RegisterUser::class)->execute([
            'name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        // Save phone to details
        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            ['phone' => $this->phone]
        );

        // Pre-create listing
        $business = $user->businesses()->create([
            'name' => $this->company_name,
            'slug' => Str::slug($this->company_name) . '-' . Str::random(5),
            'type' => $this->directory_type,
            'email' => $this->company_email ?: $this->email,
            'phone' => $this->company_phone ?: $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'zip' => $this->zip,
            'membership_tier' => $this->membership_tier,
            'status' => 'pending',
        ]);

        if ($this->directory_type === 'contractor') {
            $business->contractorDetail()->create([
                'years_in_business' => $this->years_in_business ?: null,
                'license_number' => $this->license_number ?: null,
                'is_insured' => $this->is_insured ? true : false,
            ]);
        } else {
            $business->vendorDetail()->create([
                'years_in_business' => $this->years_in_business ?: null,
            ]);
        }

        Auth::login($user);

        // Send Welcome Email
        try {
            $welcomeTemplate = \App\Models\EmailTemplate::where('type', 'welcome')->where('is_active', true)->first();
            if ($welcomeTemplate) {
                \Illuminate\Support\Facades\Log::info("Attempting to send dynamic Welcome Email to: " . $user->email);
                Mail::to($user->email)->send(new \App\Mail\DynamicEmail($welcomeTemplate, [
                    'user_name' => $user->name,
                    'login_link' => route('dashboard'),
                ]));
                \Illuminate\Support\Facades\Log::info("Dynamic Welcome Email sent successfully to: " . $user->email);
            } else {
                \Illuminate\Support\Facades\Log::warning("Welcome Email Template not found or inactive.");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send Welcome Email: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        // Send Promo Code Email
        try {
            $promoTemplate = \App\Models\EmailTemplate::where('type', 'promo_code')->where('is_active', true)->first();
            if ($promoTemplate) {
                \Illuminate\Support\Facades\Log::info("Attempting to send dynamic Promo Code Email to: " . $user->email);
                sleep(1);
                Mail::to($user->email)->send(new \App\Mail\DynamicEmail($promoTemplate, [
                    'user_name' => $user->name,
                    'promo_code' => 'WELCOME10',
                    'discount_amount' => '10%',
                ]));
                \Illuminate\Support\Facades\Log::info("Dynamic Promo Code Email sent successfully to: " . $user->email);
            } else {
                \Illuminate\Support\Facades\Log::warning("Promo Code Email Template not found or inactive.");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send Promo Code Email: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        return redirect()->route('membership.subscribe_form', ['plan' => $this->membership_tier]);
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.auth.register-wizard');
    }
}
