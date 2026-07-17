<?php

namespace App\Filament\Resources\MembershipStatuses\Pages;

use App\Filament\Resources\MembershipStatuses\MembershipStatusResource;
use Filament\Resources\Pages\ListRecords;

class ListMembershipStatuses extends ListRecords
{
    protected static string $resource = MembershipStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
