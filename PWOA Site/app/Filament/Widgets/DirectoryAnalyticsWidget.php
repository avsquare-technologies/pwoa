<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DirectoryAnalyticsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $contractorsActive = Business::where('type', 'contractor')->where('status', 'approved')->count();
        $contractorsPending = Business::where('type', 'contractor')->where('status', 'pending')->count();
        
        $vendorsActive = Business::where('type', 'vendor')->where('status', 'approved')->count();
        $vendorsPending = Business::where('type', 'vendor')->where('status', 'pending')->count();

        $goldMembers = Business::where('membership_tier', 'gold')->count();
        $totalViews = Business::sum('views_count');

        return [
            Stat::make('Contractors', $contractorsActive)
                ->description($contractorsPending . ' pending approval')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Vendors', $vendorsActive)
                ->description($vendorsPending . ' pending approval')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make('Gold Memberships', $goldMembers)
                ->description('Premium priority placement')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),
            Stat::make('Total Profile Views', number_format($totalViews))
                ->description('Accumulated view counters')
                ->descriptionIcon('heroicon-m-eye')
                ->color('gray'),
        ];
    }
}
