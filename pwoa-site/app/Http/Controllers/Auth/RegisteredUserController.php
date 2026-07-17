<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\RegisterOtpMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request and send OTP.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        // Store registration details and OTP in session
        session([
            'registration_data' => $request->only('name', 'email', 'password'),
            'registration_otp' => $otp,
            'registration_otp_expires_at' => now()->addMinutes(15),
        ]);

        // Send OTP via email
        Mail::to($request->email)->send(new RegisterOtpMail([
            'name' => $request->name,
            'otp' => $otp,
        ]));

        return redirect()->route('register.otp');
    }

    /**
     * Display the OTP verification view.
     */
    public function otpView(): View|RedirectResponse
    {
        if (!session()->has('registration_data')) {
            return redirect()->route('register');
        }

        return view('auth.register-otp');
    }

    /**
     * Handle the OTP verification request.
     */
    public function otpVerify(Request $request): RedirectResponse
    {
        if (!session()->has('registration_data')) {
            return redirect()->route('register');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $sessionOtp = session('registration_otp');
        $expiresAt = session('registration_otp_expires_at');

        if (!$sessionOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return back()->withErrors(['otp' => 'The verification code has expired. Please request a new one.']);
        }

        if ($request->otp !== $sessionOtp) {
            return back()->withErrors(['otp' => 'The verification code is invalid.']);
        }

        // OTP is correct! Execute registration action.
        $registrationData = session('registration_data');
        $user = app(RegisterUser::class)->execute($registrationData);

        // Clear OTP-related sessions
        session()->forget(['registration_data', 'registration_otp', 'registration_otp_expires_at']);

        // Log the user in
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

        return redirect(route('membership.subscribe_form', absolute: false))
            ->with('success', 'Email verified and account registered successfully!');
    }

    /**
     * Resend verification OTP code.
     */
    public function otpResend(Request $request): RedirectResponse
    {
        if (!session()->has('registration_data')) {
            return redirect()->route('register');
        }

        // Generate a new 6-digit OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        session([
            'registration_otp' => $otp,
            'registration_otp_expires_at' => now()->addMinutes(15),
        ]);

        $email = session('registration_data.email');
        $name = session('registration_data.name');

        // Send OTP via email
        Mail::to($email)->send(new RegisterOtpMail([
            'name' => $name,
            'otp' => $otp,
        ]));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }
}
