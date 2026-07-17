<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Mail\ContactFormMail;
use App\Mail\ContactAcknowledgementMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact.index');
    }

 public function send(Request $request)
{
    $data = $request->validate([
        'inquiry_type' => ['required', 'string', 'max:50'],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'subject' => ['required', 'string', 'max:255'],
        'message' => ['required', 'string', 'max:5000'],
        'phone' => ['nullable', 'string'],
        'company' => ['nullable', 'string'],
    ]);


    ContactMessage::create($data);


    // Send email to admin
    Mail::to(config('services.admin_email'))->send(new ContactFormMail($data));

    // Send acknowledgement email to the user
    Mail::to($data['email'])->send(new ContactAcknowledgementMail($data));

    return redirect()
        ->route('contact')
        ->with('contact_success', 'Message sent successfully!');
}
}

