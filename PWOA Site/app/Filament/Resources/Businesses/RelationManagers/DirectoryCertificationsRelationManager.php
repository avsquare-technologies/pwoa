<?php

namespace App\Filament\Resources\Businesses\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Support\Facades\Storage;

class DirectoryCertificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'directoryCertifications';

    protected static ?string $title = 'Directory Certifications';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('certificate_number')
                    ->label('Certificate Number'),
                DatePicker::make('issued_at')
                    ->label('Issued At'),
                DatePicker::make('expires_at')
                    ->label('Expires At'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
                FileUpload::make('document_path')
                    ->label('Certificate Document')
                    ->directory('business-certifications')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->openable()
                    ->downloadable(),
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
                TextColumn::make('certificate_number')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('issued_at')
                    ->date()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable()
                    ->placeholder('Never Expires'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('document_path')
                    ->label('Document')
                    ->formatStateUsing(fn ($state) => $state ? 'View Document' : '-')
                    ->url(fn ($state) => $state ? Storage::url($state) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('certificate_number'),
                        DatePicker::make('issued_at'),
                        DatePicker::make('expires_at'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                        FileUpload::make('document_path')
                            ->label('Certificate Document')
                            ->directory('business-certifications')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->openable()
                            ->downloadable(),
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
