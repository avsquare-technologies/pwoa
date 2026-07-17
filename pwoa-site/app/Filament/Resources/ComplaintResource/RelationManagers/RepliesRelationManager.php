<?php

namespace App\Filament\Resources\ComplaintResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    protected static ?string $recordTitleAttribute = 'message';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('admin_id')
                    ->default(fn () => Auth::id()),
                Forms\Components\Hidden::make('user_id')
                    ->default(null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender')
                    ->label('From')
                    ->state(fn ($record) => $record->sender()?->name ?? 'Unknown'),
                Tables\Columns\TextColumn::make('message')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Reply')
                    ->modalHeading('Add Admin Reply')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['admin_id'] = Auth::id();
                        $data['user_id'] = null;
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
