@inject('balanceService', 'App\Services\WashBalanceService')

@php
    $user = auth()->user();
    $hasAccess = $user ? $balanceService->hasRequiredBalance($user) : false;
    $requiredAmount = \App\Services\WashBalanceService::MIN_BALANCE;
@endphp

<div>
    @if(!$hasAccess)
        <div class="relative w-full h-full">

            <!-- FULL SCREEN OVERLAY -->
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-white/60 backdrop-blur-sm">
                <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md border">
                    <h3 class="text-2xl font-bold mb-3">Upgrade Required</h3>
                    <p class="mb-5">
                        You need at least <strong>{{ number_format(\App\Services\WashBalanceService::MIN_BALANCE) }} WASH
                            tokens</strong>.
                    </p>
                    <a href="{{ route('token.purchase') }}" class="btn btn-primary w-100">
                        Purchase Tokens
                    </a>
                </div>
            </div>

            <!-- BLURRED CONTENT -->
            <div class="blur-sm pointer-events-none select-none opacity-60">
                {{ $slot }}
            </div>

        </div>
    @else
        <!-- Render normally -->
        {{ $slot }}
    @endif
</div>