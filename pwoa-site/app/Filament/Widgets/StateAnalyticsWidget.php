<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use Filament\Widgets\ChartWidget;

class StateAnalyticsWidget extends ChartWidget
{
    protected ?string $heading = 'Directory Listings by US State';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $businesses = Business::where('status', 'approved')
            ->with('state')
            ->get();

        $grouped = $businesses->groupBy(function ($biz) {
            return $biz->state ? ($biz->state->code ?? $biz->state->name) : 'Unknown';
        });

        $stateCounts = [];
        foreach ($grouped as $state => $items) {
            $stateCounts[$state] = count($items);
        }

        arsort($stateCounts);
        $topStates = array_slice($stateCounts, 0, 10, true);

        $labels = array_keys($topStates);
        $data = array_values($topStates);

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Approved Listings',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
