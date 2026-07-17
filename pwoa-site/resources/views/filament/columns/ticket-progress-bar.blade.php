@php
    $percentage = $record->total > 0 ? min(100, round(($record->minted / $record->total) * 100, 2)) : 0;
    $status = $record->status;
    $displayedMinted = min($record->total, $record->minted);
    
    $barColor = match($status) {
        'completed' => 'bg-success-600',
        'failed' => 'bg-danger-600',
        'minting' => 'bg-primary-600 animate-pulse',
        default => 'bg-gray-400',
    };
@endphp

<div class="flex flex-col w-full gap-1.5 py-1 min-w-[120px]">
    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-tight">
        <span class="text-gray-600 dark:text-gray-400">
            @if($status === 'completed')
                <span class="flex items-center gap-1 text-success-600">
                    ✅ Verified
                </span>
            @else
                {{ $displayedMinted }} / {{ $record->total }}
            @endif
        </span>
        <span class="text-gray-500">{{ $percentage }}%</span></div>
    <div class="w-full bg-gray-200 rounded-sm dark:bg-gray-700 overflow-hidden" style="height: 6px;">
        <div class="{{ $barColor }} rounded-sm transition-all duration-500 ease-out" 
             style="width: {{ $percentage }}%; height: 6px;"></div>
    </div>
    @if($status === 'failed')
        <span class="text-[9px] text-danger-600 font-bold uppercase tracking-wider">Error Occurred</span>
    @elseif($status === 'minting')
         <span class="text-[9px] text-primary-600 font-bold uppercase tracking-wider animate-pulse">Minting...</span>
    @endif
</div>
