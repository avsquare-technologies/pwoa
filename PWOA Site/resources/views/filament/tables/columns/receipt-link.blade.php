<div>
    @if($getRecord()->receipt_url)
        <a href="{{ $getRecord()->receipt_url }}" target="_blank" class="text-primary-600 hover:text-primary-500 underline text-sm font-medium">
            View Receipt
        </a>
    @else
        <span class="text-gray-400 text-sm italic">No receipt</span>
    @endif
</div>
