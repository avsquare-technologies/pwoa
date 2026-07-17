<?php

namespace App\Filament\Widgets;

use App\Models\BusinessCategory;
use Filament\Widgets\ChartWidget;

class CategoryAnalyticsWidget extends ChartWidget
{
    protected ?string $heading = 'Directory Listings by Category';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $categories = BusinessCategory::withCount('businesses')
            ->orderByDesc('businesses_count')
            ->take(6)
            ->get();

        $labels = [];
        $data = [];

        foreach ($categories as $cat) {
            $labels[] = $cat->name;
            $data[] = $cat->businesses_count;
        }

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Listings Count',
                    'data' => $data,
                    'backgroundColor' => [
                        '#3B82F6', // Blue
                        '#10B981', // Green
                        '#F59E0B', // Amber
                        '#EC4899', // Pink
                        '#8B5CF6', // Purple
                        '#EF4444', // Red
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
