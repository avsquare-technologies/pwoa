<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectoryCertificationResource\Pages\ManageDirectoryCertifications;
use App\Models\DirectoryCertification;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class DirectoryCertificationResource extends Resource
{
    protected static ?string $model = DirectoryCertification::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Business Directory';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('badge_icon_path')
                    ->image()
                    ->directory('directory-badges')
                    ->label('Badge Icon'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('badge_icon_path')
                    ->label('Badge')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDirectoryCertifications::route('/'),
        ];
    }
}
