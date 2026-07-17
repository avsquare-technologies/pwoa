@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
        <div class="flex justify-between items-center border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-900">Complaint Status</h2>
            <span class="inline-block px-3 py-1 font-semibold leading-tight rounded-full bg-{{ $complaint->status->getColor() == 'warning' ? 'yellow' : ($complaint->status->getColor() == 'danger' ? 'red' : ($complaint->status->getColor() == 'success' ? 'green' : 'gray')) }}-200 text-xs">
                {{ $complaint->status->getLabel() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Ticket ID</p>
                <p class="text-lg font-mono">{{ $complaint->ticket_id }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Submitted On</p>
                <p>{{ $complaint->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Category</p>
                <p>{{ $complaint->category->name }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Last Update</p>
                <p>{{ $complaint->updated_at->diffForHumans() }}</p>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-lg font-bold mb-2">Subject: {{ $complaint->title }}</h3>
            <div class="bg-gray-50 p-4 rounded-lg text-gray-700">
                {{ $complaint->description }}
            </div>
        </div>

        @if($complaint->replies->count() > 0)
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-bold mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    @foreach($complaint->replies->whereNotNull('admin_id')->take(3) as $reply)
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <p class="text-xs font-bold text-blue-600 mb-1">Support Team • {{ $reply->created_at->diffForHumans() }}</p>
                            <p class="text-sm text-gray-800">{{ $reply->message }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10 text-center space-x-4">
            <a href="{{ route('complaints.track') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                Track Another
            </a>
            @auth
                <a href="{{ route('complaints.show', $complaint) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Go to Chat
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Login for full history
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
