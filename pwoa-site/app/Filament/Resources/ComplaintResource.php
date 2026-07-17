<?php

namespace App\Filament\Resources;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Filament\Resources\ComplaintResource\Pages;
use App\Filament\Resources\ComplaintResource\RelationManagers;
use App\Models\Complaint;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';
    protected static string | \UnitEnum | null $navigationGroup = 'Complaint Management';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Complaint Details')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_id')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('title')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('attachment_path')
                            ->label('Attachment')
                            ->content(function ($record) {
                                if (!$record || !$record->attachment_path) {
                                    return 'No attachment';
                                }

                                $url = asset('storage/' . $record->attachment_path);
                                $extension = strtolower(pathinfo($record->attachment_path, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);

                                if ($isImage) {
                                    return new \Illuminate\Support\HtmlString("
                                        <div class='space-y-2'>
                                            <a href='{$url}' target='_blank' class='inline-block border rounded-lg overflow-hidden shadow-sm hover:opacity-90 transition-opacity'>
                                                <img src='{$url}' alt='Attachment Preview' class='max-h-48 object-cover' />
                                            </a>
                                            <div>
                                                <a href='{$url}' target='_blank' class='text-primary-600 dark:text-primary-400 hover:underline font-medium text-sm inline-flex items-center gap-1'>
                                                    <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24' style='display: inline-block;'>
                                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14' />
                                                    </svg>
                                                    View Full Image
                                                </a>
                                            </div>
                                        </div>
                                    ");
                                }

                                return new \Illuminate\Support\HtmlString("
                                    <div class='flex items-center gap-2 p-2 border rounded-lg bg-gray-50 dark:bg-gray-800 w-fit'>
                                        <svg class='w-8 h-8 text-gray-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />
                                        </svg>
                                        <div class='flex flex-col'>
                                            <span class='text-xs text-gray-500 font-mono'>" . basename($record->attachment_path) . "</span>
                                            <a href='{$url}' target='_blank' class='text-primary-600 dark:text-primary-400 hover:underline font-medium text-sm inline-flex items-center gap-1'>
                                                <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24' style='display: inline-block;'>
                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4' />
                                                </svg>
                                                Download Attachment
                                            </a>
                                        </div>
                                    </div>
                                ");
                            })
                            ->visible(fn ($record) => $record && $record->attachment_path),
                    ])->columns(2),
                
                Section::make('Management')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(ComplaintStatus::class)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('priority')
                            ->options(ComplaintPriority::class)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignee', 'name', fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'super_admin')))
                            ->searchable()
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ComplaintStatus $state): string => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (ComplaintPriority $state): string => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->placeholder('Unassigned')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ComplaintStatus::class),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(ComplaintPriority::class),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }
}
