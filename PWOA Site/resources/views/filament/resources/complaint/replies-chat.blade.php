@php
/** @var \Illuminate\Support\Collection|\App\Models\ComplaintReply[] $records */
@endphp

<div class="space-y-4">

@foreach($records->sortByDesc('created_at') as $reply)
    @php
        $isAdmin = $reply->admin_id !== null;
        $senderName = $reply->sender()?->name ?? 'System';
        $attachment = $reply->attachment;
        $isImage = $attachment && \Illuminate\Support\Str::startsWith(
            mime_content_type(storage_path('app/' . $attachment)),
            'image/'
        );
    @endphp

    <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[70%]">
            <div class="flex items-center mb-1 {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                <span class="text-xs font-medium {{ $isAdmin ? 'text-blue-600' : 'text-gray-600' }}">
                    {{ $senderName }}
                </span>
                <span class="mx-2 text-xs text-gray-500">
                    {{ $reply->created_at->format('M d, Y h:i A') }}
                </span>
            </div>

            <div class="rounded-xl p-3 {{ $isAdmin ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} break-words">
                {{ $reply->message }}
            </div>

            @if($attachment)
                <div class="mt-2">
                    @if($isImage)
                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank">
                            <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment"
                                 class="rounded-md max-w-full h-auto border">
                        </a>
                    @else
                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank"
                           class="inline-flex items-center text-sm text-blue-600 hover:underline">
                            <i class="bi bi-paperclip me-1"></i> Download attachment
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endforeach

</div>

{{-- Pagination (optional) --}}
@if(method_exists($records, 'links'))
    <div class="mt-4">
        {{ $records->links() }}
    </div>
@endif
