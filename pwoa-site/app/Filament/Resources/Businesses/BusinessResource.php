<?php

namespace App\Filament\Resources\Businesses;

use App\Filament\Resources\Businesses\Pages\ManageBusinesses;
use App\Models\Business;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Business Directory';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Primary Details')
                            ->columns(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Select::make('type')
                                    ->options(['contractor' => 'Contractor', 'vendor' => 'Vendor'])
                                    ->default('contractor')
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Branding & Description')
                            ->columns(2)
                            ->schema([
                                FileUpload::make('logo_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('business-logos')
                                    ->label('Business Logo'),
                                FileUpload::make('cover_photo_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('business-banners')
                                    ->label('Cover Photo'),
                                FileUpload::make('featured_image_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('business-featured')
                                    ->label('Featured Image')
                                    ->columnSpanFull(),
                                TextInput::make('tagline')
                                    ->maxLength(150)
                                    ->columnSpanFull(),
                                Textarea::make('short_description')
                                    ->rows(2)
                                    ->maxLength(250)
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Contact & Location')
                            ->columns(2)
                            ->schema([
                                TextInput::make('email')->email()->required(),
                                TextInput::make('phone')->tel(),
                                TextInput::make('website')->url()->columnSpanFull(),
                                TextInput::make('address')->columnSpanFull(),
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->default(1)
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Select::make('state_id')
                                    ->relationship('state', 'name', fn ($query, $get) => 
                                        $query->where('country_id', $get('country_id'))
                                    )
                                    ->searchable()
                                    ->live()
                                    ->required(),
                                Select::make('city_id')
                                    ->relationship('city', 'name', fn ($query, $get) => 
                                        $query->where('state_id', $get('state_id'))
                                    )
                                    ->searchable()
                                    ->required(),
                                TextInput::make('zip')->required(),
                            ]),

                        Group::make([
                            Section::make('Contractor Details')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('years_in_business')
                                        ->numeric()
                                        ->label('Years in Business')
                                        ->minValue(0),
                                    TextInput::make('license_number')
                                        ->label('License Number (Optional)'),
                                     FileUpload::make('license_path')
                                         ->label('License Document')
                                         ->directory('business-licenses')
                                         ->acceptedFileTypes(['application/pdf', 'image/*'])
                                         ->openable()
                                         ->downloadable(),
                                     Select::make('license_status')
                                         ->label('License Verification Status')
                                         ->options([
                                             'pending' => 'Pending',
                                             'verified' => 'Verified',
                                             'rejected' => 'Rejected',
                                         ])
                                         ->default('pending')
                                         ->required(),
                                     Select::make('service_radius_id')
                                         ->relationship('serviceRadius', 'name')
                                         ->label('Service Radius')
                                         ->columnSpanFull(),
                                     Grid::make(2)
                                         ->columnSpanFull()
                                         ->schema([
                                             Toggle::make('is_insured')
                                                 ->label('Fully Insured'),
                                             Toggle::make('is_emergency_service')
                                                 ->label('Emergency Service'),
                                             Toggle::make('is_subcontracting')
                                                 ->label('Subcontracting'),
                                             Toggle::make('is_national_accounts')
                                                 ->label('National Accounts'),
                                         ]),
                                     FileUpload::make('insurance_path')
                                         ->label('Insurance Document')
                                         ->directory('business-insurance')
                                         ->acceptedFileTypes(['application/pdf', 'image/*'])
                                         ->openable()
                                         ->downloadable(),
                                     Select::make('insurance_status')
                                         ->label('Insurance Verification Status')
                                         ->options([
                                             'pending' => 'Pending',
                                             'verified' => 'Verified',
                                             'rejected' => 'Rejected',
                                         ])
                                         ->default('pending')
                                         ->required(),
                                ])
                        ])
                        ->relationship('contractorDetail')
                        ->visible(fn ($get) => $get('type') === 'contractor'),

                        Group::make([
                            Section::make('Vendor Details')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('years_in_business')
                                        ->numeric()
                                        ->label('Years in Business')
                                        ->minValue(0)
                                        ->columnSpanFull(),
                                    Grid::make(2)
                                        ->columnSpanFull()
                                        ->schema([
                                            Toggle::make('has_online_ordering')
                                                ->label('Online Ordering'),
                                            Toggle::make('has_local_pickup')
                                                ->label('Local Pickup'),
                                            Toggle::make('has_member_discounts')
                                                ->label('Member Discounts'),
                                            Toggle::make('wants_preferred_program')
                                                ->label('Wants Preferred Vendor'),
                                            Toggle::make('wants_partnership')
                                                ->label('Wants Partnership'),
                                        ]),
                                ])
                        ])
                        ->relationship('vendorDetail')
                        ->visible(fn ($get) => $get('type') === 'vendor'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Status & Visibility')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'suspended' => 'Suspended',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->rules([
                                        fn ($get, $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            if ($value === 'approved' && $get('type') === 'contractor') {
                                                $errors = [];
                                                
                                                $licensePath = $get('contractorDetail.license_path') ?? $record?->contractorDetail?->license_path;
                                                $licenseStatus = $get('contractorDetail.license_status') ?? $record?->contractorDetail?->license_status;
                                                $insurancePath = $get('contractorDetail.insurance_path') ?? $record?->contractorDetail?->insurance_path;
                                                $insuranceStatus = $get('contractorDetail.insurance_status') ?? $record?->contractorDetail?->insurance_status;
                                                
                                                if ($licensePath && $licenseStatus !== 'verified') {
                                                    $errors[] = 'License document is not verified.';
                                                }
                                                if ($insurancePath && $insuranceStatus !== 'verified') {
                                                    $errors[] = 'Insurance document is not verified.';
                                                }
                                                
                                                if ($record) {
                                                    $pendingCerts = $record->directoryCertifications()
                                                        ->wherePivot('status', '!=', 'approved')
                                                        ->get();
                                                    if ($pendingCerts->isNotEmpty()) {
                                                        foreach ($pendingCerts as $cert) {
                                                            $errors[] = 'Certification "' . $cert->name . '" is not approved.';
                                                        }
                                                    }
                                                }
                                                
                                                if (!empty($errors)) {
                                                    $fail('Cannot approve listing: ' . implode(' ', $errors));
                                                }
                                            }
                                        }
                                    ]),
                                Select::make('membership_tier')
                                    ->options([
                                        'standard' => 'Standard',
                                        'gold' => 'Gold',
                                    ])
                                    ->default('standard')
                                    ->required(),
                                Toggle::make('featured')
                                    ->label('Featured Listing')
                                    ->default(false),
                                Toggle::make('is_verified')
                                    ->label('Verified Business')
                                    ->default(false),
                                Toggle::make('is_preferred')
                                    ->label('Preferred Vendor')
                                    ->default(false)
                                    ->visible(fn ($get) => $get('type') === 'vendor'),
                            ]),

                        Section::make('Associations')
                            ->schema([
                                Select::make('categories')
                                    ->multiple()
                                    ->relationship('categories', 'name', fn ($query, $get) => 
                                        $query->where('type', $get('type'))->where('category_type', 'child')
                                    )
                                    ->label('Categories')
                                    ->required(),
                                Select::make('directoryCertifications')
                                    ->multiple()
                                    ->relationship('directoryCertifications', 'name')
                                    ->preload()
                                    ->label('Certifications (Contractors Only)')
                                    ->visible(fn ($get) => $get('type') === 'contractor'),
                                Select::make('directoryEquipments')
                                    ->multiple()
                                    ->relationship('directoryEquipments', 'name')
                                    ->preload()
                                    ->label('Equipment Fleet (Contractors Only)')
                                    ->visible(fn ($get) => $get('type') === 'contractor'),
                            ]),

                        Section::make('Social Media Links')
                            ->schema([
                                TextInput::make('facebook')->url(),
                                TextInput::make('instagram')->url(),
                                TextInput::make('linkedin')->url(),
                                TextInput::make('youtube')->url(),
                                TextInput::make('tiktok')->url(),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Primary Details')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('user.name')->label('Owner/User'),
                                TextEntry::make('name'),
                                TextEntry::make('slug'),
                                TextEntry::make('type')->badge(),
                            ]),

                        Section::make('Branding & Description')
                            ->columns(2)
                            ->schema([
                                ImageEntry::make('logo_path')->label('Logo'),
                                ImageEntry::make('cover_photo_path')->label('Cover Banner'),
                                ImageEntry::make('featured_image_path')->label('Featured Image')->columnSpanFull(),
                                TextEntry::make('tagline')->columnSpanFull()->placeholder('-'),
                                TextEntry::make('short_description')->columnSpanFull()->placeholder('-'),
                                TextEntry::make('description')->html()->columnSpanFull()->placeholder('-'),
                            ]),

                        Section::make('Contact & Location')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('email')->label('Email Address')->placeholder('-'),
                                TextEntry::make('phone')->placeholder('-'),
                                TextEntry::make('website')->placeholder('-')->columnSpanFull(),
                                TextEntry::make('address')->placeholder('-')->columnSpanFull(),
                                TextEntry::make('country.name')->label('Country')->placeholder('-'),
                                TextEntry::make('state.name')->label('State')->placeholder('-'),
                                TextEntry::make('city.name')->label('City')->placeholder('-'),
                                TextEntry::make('zip')->placeholder('-'),
                            ]),

                        Group::make([
                            Section::make('Contractor Details')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('contractorDetail.years_in_business')->label('Years in Business')->placeholder('-'),
                                    TextEntry::make('contractorDetail.license_number')->label('License Number')->placeholder('-'),
                                    TextEntry::make('contractorDetail.license_path')
                                        ->label('License Document')
                                        ->formatStateUsing(fn ($state) => $state ? 'View License Document' : '-')
                                        ->url(fn ($state) => $state ? Storage::url($state) : null)
                                        ->openUrlInNewTab()
                                        ->color('primary')
                                        ->placeholder('-'),
                                    TextEntry::make('contractorDetail.license_status')
                                        ->label('License Status')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'verified' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                            default => 'gray',
                                        })
                                        ->placeholder('Pending'),
                                    TextEntry::make('contractorDetail.serviceRadius.name')->label('Service Radius')->placeholder('-')->columnSpanFull(),
                                    IconEntry::make('contractorDetail.is_insured')->label('Fully Insured')->boolean(),
                                    IconEntry::make('contractorDetail.is_emergency_service')->label('Emergency Service')->boolean(),
                                    IconEntry::make('contractorDetail.is_subcontracting')->label('Subcontracting')->boolean(),
                                    IconEntry::make('contractorDetail.is_national_accounts')->label('National Accounts')->boolean(),
                                    TextEntry::make('contractorDetail.insurance_path')
                                        ->label('Insurance Document')
                                        ->formatStateUsing(fn ($state) => $state ? 'View Insurance Document' : '-')
                                        ->url(fn ($state) => $state ? Storage::url($state) : null)
                                        ->openUrlInNewTab()
                                        ->color('primary')
                                        ->placeholder('-'),
                                    TextEntry::make('contractorDetail.insurance_status')
                                        ->label('Insurance Status')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'verified' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                            default => 'gray',
                                        })
                                        ->placeholder('Pending'),
                                ])
                        ])->visible(fn ($record) => $record?->type === 'contractor'),

                        Group::make([
                            Section::make('Vendor Details')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('vendorDetail.years_in_business')->label('Years in Business')->placeholder('-')->columnSpanFull(),
                                    IconEntry::make('vendorDetail.has_online_ordering')->label('Online Ordering')->boolean(),
                                    IconEntry::make('vendorDetail.has_local_pickup')->label('Local Pickup')->boolean(),
                                    IconEntry::make('vendorDetail.has_member_discounts')->label('Member Discounts')->boolean(),
                                    IconEntry::make('vendorDetail.wants_preferred_program')->label('Wants Preferred Vendor')->boolean(),
                                    IconEntry::make('vendorDetail.wants_partnership')->label('Wants Partnership')->boolean(),
                                ])
                        ])->visible(fn ($record) => $record?->type === 'vendor'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Status & Visibility')
                            ->schema([
                                TextEntry::make('status')->badge(),
                                TextEntry::make('membership_tier')->badge(),
                                IconEntry::make('featured')->boolean(),
                                IconEntry::make('is_verified')->boolean(),
                                IconEntry::make('is_preferred')->boolean()->visible(fn ($record) => $record?->type === 'vendor'),
                                TextEntry::make('verified_at')->dateTime()->placeholder('Not Verified'),
                                TextEntry::make('views_count')->label('Total Views')->numeric()->placeholder('0'),
                                TextEntry::make('avg_rating')->label('Average Rating')->numeric()->placeholder('No ratings'),
                            ]),

                        Section::make('Associations')
                            ->schema([
                                TextEntry::make('categories.name')->badge()->label('Categories')->placeholder('-'),
                                TextEntry::make('directoryCertifications.name')->badge()->label('Certifications')->placeholder('-')->visible(fn ($record) => $record?->type === 'contractor'),
                                TextEntry::make('directoryEquipments.name')->badge()->label('Equipment')->placeholder('-')->visible(fn ($record) => $record?->type === 'contractor'),
                            ]),

                        Section::make('Social Media Links')
                            ->schema([
                                TextEntry::make('facebook')->placeholder('-'),
                                TextEntry::make('instagram')->placeholder('-'),
                                TextEntry::make('linkedin')->placeholder('-'),
                                TextEntry::make('youtube')->placeholder('-'),
                                TextEntry::make('tiktok')->placeholder('-'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->name),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('membership_tier')
                    ->label('Tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gold' => 'warning',
                        default => 'gray',
                    }),
                ToggleColumn::make('featured')
                    ->label('Featured'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('city.name')
                    ->label('City')
                    ->sortable(),
                TextColumn::make('state.name')
                    ->label('State')
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('Not Verified'),
                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('avg_rating')
                    ->label('Rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'contractor' => 'Contractor',
                        'vendor' => 'Vendor',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('membership_tier')
                    ->options([
                        'standard' => 'Standard',
                        'gold' => 'Gold',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Business $record) => $record->status === 'pending')
                    ->modalHeading('Approve Business Listing & Verify Documents')
                    ->modalSubmitActionLabel('Approve & Verify')
                    ->form(function (Business $record) {
                        $schema = [];
                        
                        if ($record->type === 'contractor') {
                            $detail = $record->contractorDetail;
                            
                            if ($detail) {
                                if ($detail->license_path) {
                                    $schema[] = Placeholder::make('license_link')
                                        ->label('Contractor License File')
                                        ->content(new \Illuminate\Support\HtmlString(
                                            '<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 10px;">
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <div style="padding: 8px; background-color: #f0f9ff; border-radius: 6px; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                                                        <svg style="width: 24px; height: 24px; min-width: 24px; min-height: 24px;" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div style="display: flex; flex-direction: column;">
                                                        <span style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; line-height: 1.25;">License Document</span>
                                                        <p style="font-size: 11px; color: #6b7280; margin: 2px 0 0 0;">License Number: ' . e($detail->license_number ?: 'Not Provided') . '</p>
                                                    </div>
                                                </div>
                                                <a href="' . Storage::url($detail->license_path) . '" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 14px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #0284c7; border-radius: 6px; text-decoration: none; border: none; cursor: pointer;">
                                                    View Document
                                                </a>
                                            </div>'
                                        ));
                                        
                                    $schema[] = Toggle::make('confirm_license')
                                        ->label('I have reviewed and verified the License Document')
                                        ->required()
                                        ->accepted();
                                }
                                
                                if ($detail->insurance_path) {
                                    $schema[] = Placeholder::make('insurance_link')
                                        ->label('Insurance Certificate File')
                                        ->content(new \Illuminate\Support\HtmlString(
                                            '<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 10px;">
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <div style="padding: 8px; background-color: #f0f9ff; border-radius: 6px; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                                                        <svg style="width: 24px; height: 24px; min-width: 24px; min-height: 24px;" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div style="display: flex; flex-direction: column;">
                                                        <span style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; line-height: 1.25;">Insurance Document</span>
                                                        <p style="font-size: 11px; color: #6b7280; margin: 2px 0 0 0;">Status: ' . e(ucfirst($detail->insurance_status)) . '</p>
                                                    </div>
                                                </div>
                                                <a href="' . Storage::url($detail->insurance_path) . '" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 14px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #0284c7; border-radius: 6px; text-decoration: none; border: none; cursor: pointer;">
                                                    View Document
                                                </a>
                                            </div>'
                                        ));
                                        
                                    $schema[] = Toggle::make('confirm_insurance')
                                        ->label('I have reviewed and verified the Insurance Document')
                                        ->required()
                                        ->accepted();
                                }
                            }
                            
                            $certsWithDocs = $record->directoryCertifications()
                                ->wherePivotNotNull('document_path')
                                ->get();
                                
                            if ($certsWithDocs->count() > 0) {
                                $links = '';
                                foreach ($certsWithDocs as $cert) {
                                    $details = [];
                                    if ($cert->pivot->certificate_number) {
                                        $details[] = 'Cert #: ' . e($cert->pivot->certificate_number);
                                    }
                                    if ($cert->pivot->issued_at) {
                                        $details[] = 'Issued: ' . e(\Carbon\Carbon::parse($cert->pivot->issued_at)->format('M d, Y'));
                                    }
                                    if ($cert->pivot->expires_at) {
                                        $details[] = 'Expires: ' . e(\Carbon\Carbon::parse($cert->pivot->expires_at)->format('M d, Y'));
                                    } else {
                                        $details[] = 'Expires: Never';
                                    }
                                    $detailsText = implode(' | ', $details);
 
                                    $links .= '<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 10px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="padding: 8px; background-color: #f0f9ff; border-radius: 6px; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                                                <svg style="width: 24px; height: 24px; min-width: 24px; min-height: 24px;" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div style="display: flex; flex-direction: column;">
                                                <span style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; line-height: 1.25;">' . e($cert->name) . '</span>
                                                <p style="font-size: 11px; color: #6b7280; margin: 2px 0 0 0;">Status: ' . e(ucfirst($cert->pivot->status)) . '</p>
                                                <p style="font-size: 11px; color: #9ca3af; margin: 2px 0 0 0;">' . e($detailsText) . '</p>
                                            </div>
                                        </div>
                                        <a href="' . Storage::url($cert->pivot->document_path) . '" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 14px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #0284c7; border-radius: 6px; text-decoration: none; border: none; cursor: pointer;">
                                            View Proof
                                        </a>
                                    </div>';
                                }
                                
                                $schema[] = Placeholder::make('certs_links')
                                    ->label('Directory Certification Proofs')
                                    ->content(new \Illuminate\Support\HtmlString($links));
                                    
                                $schema[] = Toggle::make('confirm_certs')
                                    ->label('I have reviewed and verified all Directory Certification documents')
                                    ->required()
                                    ->accepted();
                            }
                        }
                        
                        if (empty($schema)) {
                            $schema[] = Placeholder::make('no_docs')
                                ->content('No special documents uploaded for this business. You can proceed with standard approval.');
                        }
                        
                        return $schema;
                    })
                    ->action(function (Business $record) {
                        $oldStatus = $record->status;
                        
                        // Update business status to approved
                        $record->update([
                            'status' => 'approved',
                            'rejection_reason' => null,
                            'verified_at' => now(),
                        ]);
                        
                        // Automatically update contractor details document statuses
                        if ($record->type === 'contractor') {
                            $detail = $record->contractorDetail;
                            if ($detail) {
                                $detailUpdate = [];
                                if ($detail->license_path) {
                                    $detailUpdate['license_status'] = 'verified';
                                }
                                if ($detail->insurance_path) {
                                    $detailUpdate['insurance_status'] = 'verified';
                                }
                                if (!empty($detailUpdate)) {
                                    $detail->update($detailUpdate);
                                }
                            }
                            
                            $certsWithDocs = $record->directoryCertifications()
                                ->wherePivotNotNull('document_path')
                                ->get();
                                
                            if ($certsWithDocs->count() > 0) {
                                foreach ($certsWithDocs as $cert) {
                                    $record->directoryCertifications()->updateExistingPivot($cert->id, [
                                        'status' => 'approved'
                                    ]);
                                }
                            }
                        }

                        // Log administrative action
                        app(\App\Services\Shared\AuditService::class)->log(
                            action: 'business_approved',
                            model: $record,
                            oldValues: ['status' => $oldStatus],
                            newValues: ['status' => 'approved', 'verified_at' => now()->toDateTimeString()]
                        );

                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\ListingApprovedNotification($record));
                        }
                    }),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Business $record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection/Revision Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please describe why this listing is being rejected or needs revision...'),
                    ])
                    ->action(function (Business $record, array $data) {
                        $oldStatus = $record->status;
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        // Log administrative action
                        app(\App\Services\Shared\AuditService::class)->log(
                            action: 'business_rejected',
                            model: $record,
                            oldValues: ['status' => $oldStatus],
                            newValues: ['status' => 'rejected', 'rejection_reason' => $data['rejection_reason']]
                        );

                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\ListingRejectedNotification($record, $data['rejection_reason']));
                        }
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Businesses\Pages\ListBusinesses::route('/'),
            'create' => \App\Filament\Resources\Businesses\Pages\CreateBusiness::route('/create'),
            'edit' => \App\Filament\Resources\Businesses\Pages\EditBusiness::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            // \App\Filament\Resources\Businesses\RelationManagers\BadgesRelationManager::class,
            \App\Filament\Resources\Businesses\RelationManagers\DirectoryCertificationsRelationManager::class,
        ];
    }
}
