<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\Pages\ViewEvent;
use App\Models\Event;
use App\Models\TicketBatch;
use App\Filament\Resources\TicketBatches\TicketBatchResource;
use App\Jobs\DispatchTicketChunksJob;
use App\Services\PinataService;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string | UnitEnum | null $navigationGroup = 'Events & Programs';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Identity')
                    ->description('Essential details about the event.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('event_category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->label('Event Category'),
                    ])->columnSpan(2),

                Section::make('Logistics & Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        FileUpload::make('image_path')
                            ->image()
                            ->disk('public')
                            ->directory('event-images'),
                        TextInput::make('location'),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->numeric()
                                    ->placeholder('e.g. 40.7128'),
                                TextInput::make('longitude')
                                    ->numeric()
                                    ->placeholder('e.g. -74.0060'),
                            ]),
                    ])->columnSpan(1),

                Section::make('Scheduling & Capacity')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')->required(),
                                DateTimePicker::make('ends_at')->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('capacity')
                                    ->numeric()
                                    ->placeholder('Unlimited'),
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                Toggle::make('is_free_for_members')
                                    ->label('Free for Members')
                                    ->inline(false),
                            ]),
                    ])->columnSpanFull(),
            ])->columns(3);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
    ->formatStateUsing(fn ($state) => Str::of($state)->stripTags())
    ->placeholder('-')
    ->columnSpanFull(),
                TextEntry::make('location')
                    ->placeholder('-'),
                TextEntry::make('starts_at')
                    ->dateTime(),
                TextEntry::make('ends_at')
                    ->dateTime(),
                TextEntry::make('capacity')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('price')
                    ->money(),
                IconEntry::make('is_free_for_members')
                    ->boolean(),
                ImageEntry::make('image_path')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Banner')
                    ->circular(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->location),
                TextColumn::make('starts_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('total_tickets')
                    ->label('Total')
                    ->getStateUsing(fn (Event $record) => $record->capacity ?? $record->batches()->sum('total'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('minted_tickets')
                    ->label('Minted')
                    ->getStateUsing(fn (Event $record) => min($record->capacity ?? 9999, $record->batches()->sum('minted')))
                    ->badge()
                    ->color('success'),
                TextColumn::make('attendees_count')
                    ->label('Sold')
                    ->getStateUsing(fn (Event $record) => min($record->capacity ?? 9999, $record->tickets()->where(function ($q) {
                        $q->whereIn('status', ['sold', 'minting'])
                          ->orWhere(function ($sq) {
                              $sq->where('status', 'minted')
                                 ->whereNotNull('user_id');
                          });
                    })->count()))
                    ->badge()
                    ->color('info'),
                TextColumn::make('latest_batch_status')
                    ->label('Minting')
                    ->getStateUsing(fn (Event $record) => $record->batches()->latest()->first()?->status ?? '-')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'completed' => 'success',
                        'minting' => 'warning',
                        'failed' => 'danger',
                        'queued' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'cancelled' => 'danger',
                    }),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->badge()
                    ->label('Category'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('event_category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                \Filament\Tables\Filters\Filter::make('starts_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('starts_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('starts_at', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                static::getPrepareMetadataAction(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                DeleteBulkAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPrepareMetadataAction(): Action
    {
        return Action::make('prepare_metadata')
            ->label('Prepare NFT Metadata')
            ->icon('heroicon-o-cpu-chip')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Prepare NFT Metadata')
            ->modalDescription('This will upload the event banner and generate the IPFS metadata required for on-demand minting.')
            ->modalSubmitActionLabel('Prepare Metadata')
            ->action(function (Event $record, PinataService $pinata) {
                try {
                    if (!$record->image_path) {
                        throw new \Exception("Event must have an image to prepare metadata.");
                    }

                    if ($record->batches()->exists()) {
                        throw new \Exception("Metadata is already prepared for this event.");
                    }

                    $batchId = Str::uuid()->toString();
                    $imagePath = $record->image_path;
                    $isUrl = str_starts_with($imagePath, 'http');
                    $tempFile = null;

                    if ($isUrl) {
                        $tempFile = tempnam(sys_get_temp_dir(), 'nft_');
                        $imageContent = file_get_contents($imagePath);
                        if (!$imageContent) {
                            throw new \Exception("Failed to download image from URL: {$imagePath}");
                        }
                        file_put_contents($tempFile, $imageContent);
                        $uploadPath = $tempFile;
                    } else {
                        $disk = Storage::disk('public')->exists($imagePath) ? 'public' : config('filesystems.default');
                        $uploadPath = Storage::disk($disk)->path($imagePath);
                    }

                    if (!file_exists($uploadPath)) {
                        throw new \Exception("Event image file not found on disk at: {$uploadPath}");
                    }

                    try {
                        $imageIpfsHash = $pinata->uploadFile($uploadPath);
                    } finally {
                        if ($tempFile && file_exists($tempFile)) {
                            unlink($tempFile);
                        }
                    }

                    $ticketPrice = (float) ($record->price ?? 0);

                    $metadata = [
                        'name' => "{$record->title} NFT Ticket",
                        'description' => trim(strip_tags($record->description)),
                        'image' => $imageIpfsHash,
                        'external_url' => config('app.url') . '/events/' . $record->slug,
                        'attributes' => [
                            ['trait_type' => 'Event Name', 'value' => $record->title],
                            ['trait_type' => 'Venue', 'value' => $record->location ?? 'Virtual'],
                            ['trait_type' => 'Category', 'value' => $record->category?->name ?? 'Uncategorized'],
                            ['trait_type' => 'Start Date', 'value' => $record->starts_at->toDateTimeString()],
                            ['trait_type' => 'Price (USD)', 'value' => $ticketPrice],
                            ['trait_type' => 'Ticket Type', 'value' => 'NFT Access Pass'],
                        ]
                    ];
                    $metadataIpfsHash = $pinata->uploadJson($metadata);

                    $priceWash = $ticketPrice / config('services.xrpl.wash_to_usd', 0.05);

                    TicketBatch::create([
                        "event_id" => $record->id,
                        "creator_id" => \Illuminate\Support\Facades\Auth::id(),
                        "batch_id" => $batchId,
                        "total" => $record->capacity ?? 100,
                        "minted" => 0,
                        "failed" => 0,
                        "next_index" => 1,
                        "metadata_uri" => $metadataIpfsHash,
                        "price" => $priceWash,
                        "status" => "active"
                    ]);

                    Notification::make()
                        ->title('Metadata Prepared')
                        ->body("NFT metadata successfully uploaded to IPFS and stored.")
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Preparation Failed')
                        ->danger()
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
