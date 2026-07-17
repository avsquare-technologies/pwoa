<?php

namespace App\Filament\Resources\Businesses\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;

class BadgesRelationManager extends RelationManager
{
    protected static string $relationship = 'badges';

    protected static ?string $title = 'Assigned Badges';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('assigned_at')
                    ->default(now()),
                DateTimePicker::make('expires_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->badge(),
                TextColumn::make('assigned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never Expires'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        DateTimePicker::make('assigned_at')
                            ->default(now()),
                        DateTimePicker::make('expires_at'),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
