<?php

namespace App\Filament\Resources\MembershipStatuses\Pages;

use App\Filament\Resources\MembershipStatuses\MembershipStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMembershipStatus extends CreateRecord
{
    protected static string $resource = MembershipStatusResource::class;
}
