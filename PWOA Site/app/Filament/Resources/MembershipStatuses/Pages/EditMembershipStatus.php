<?php

namespace App\Filament\Resources\MembershipStatuses\Pages;

use App\Filament\Resources\MembershipStatuses\MembershipStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMembershipStatus extends EditRecord
{
    protected static string $resource = MembershipStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
