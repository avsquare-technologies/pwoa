@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Track Your Complaint
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your Ticket ID and Email to check status
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('complaints.track.submit') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="ticket_id" class="sr-only">Ticket ID</label>
                    <input id="ticket_id" name="ticket_id" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Ticket ID (e.g. PWOA-2026-00001)">
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>
            </div>

            @if($errors->any())
                <div class="text-red-500 text-sm text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Track Now
                </button>
            </div>
        </form>

        <div class="text-center">
            <a href="/" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
