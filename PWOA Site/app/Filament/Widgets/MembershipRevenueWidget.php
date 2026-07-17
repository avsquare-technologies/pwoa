<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class MembershipRevenueWidget extends ChartWidget
{
    protected ?string $heading = 'Membership Subscription Revenue (Last 6 Months)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $payments = Payment::where('status', 'succeeded')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->orderBy('paid_at')
            ->get();

        $grouped = $payments->groupBy(function ($payment) {
            return $payment->paid_at->format('Y-m');
        });

        $labels = [];
        $data = [];

        foreach ($grouped as $month => $items) {
            $labels[] = date('F Y', strtotime($month . '-01'));
            $data[] = (float)$items->sum('amount');
        }

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $data,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
