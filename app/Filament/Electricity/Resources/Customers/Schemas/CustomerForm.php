<?php

namespace App\Filament\Electricity\Resources\Customers\Schemas;

use App\Helpers\ElectricBillHelper;
use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('fields.name'))
                    ->required()
                    ->columnSpan(3),
                TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->default(null)
                    ->columnSpan(3),
                Select::make('electric_area_id')
                    ->label(__('fields.area'))
                    ->relationship('electricArea', 'name')
                    ->required()
                    ->columnSpan(3),
                Select::make('block_id')
                    ->label(__('fields.block_name'))
                    ->relationship('block', 'bolck_name')
                    ->required()
                    ->columnSpan(3),
                TextInput::make('shop_no')
                    ->label(__('fields.shop_no'))
                    ->required()
                    ->columnSpan(3),
                TextInput::make('phone')
                    ->label(__('fields.phone'))
                    ->required()
                    ->columnSpan(3),
                TextInput::make('address')
                    ->label(__('fields.address'))
                    ->required()
                    ->default(null)
                    ->columnSpan(6),
                Section::make('দোকানের বিশদ বিবরণ')
                    ->schema([
                        TextInput::make('shop_area')
                            ->label(__('fields.shop_area'))
                            ->default(0)
                            ->required()
                            ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                            ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state)),
                        TextInput::make('central_ac_area')
                            ->label(__('fields.central_ac_area'))
                            ->default(0)
                            ->required()
                            ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                            ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state)),
                        TextInput::make('common_ac_area')
                            ->label(__('fields.common_ac_area'))
                            ->default(0)
                            ->required()
                            ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                            ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state)),
                        TextInput::make('water_area')
                            ->label(__('fields.water_area'))
                            ->default(0)
                            ->required()
                            ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                            ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state)),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                Section::make('গ্রাহকের পূর্বের বকেয়া')
                    ->schema([
                        
                    Group::make()
                        ->relationship('previousDue')
                        ->schema([
                            TextInput::make('amount')
                                ->label('বকেয়া পরিমাণ')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->columnSpan(6)
                                ,
                            Select::make('is_paid')
                                ->label('বকেয়া পরিশোধিত')
                                ->options([
                                    0 => 'না',
                                    1 => 'হ্যাঁ',
                                ])
                                ->default(0)
                                ->required()
                                ->columnSpan(6),
                            RichEditor::make('remarks')
                                ->label('মন্তব্য')
                                ->default(null)
                                ->nullable()
                                ->placeholder('এখানে নোট লিখুন...')
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('images/previous_due_remarks')
                                ->fileAttachmentsVisibility('public')
                                ->columnSpanFull(),
                        ])->columns(12)
                    ])->columnSpanFull(),
                Section::make('মিটার তথ্য')
                    ->schema([
                        Repeater::make('মিটার')
                            ->label('মিটার')
                            ->relationship('meters') 
                            ->schema([
                                TextInput::make('meter_number')
                                    ->label(__('fields.meter_number'))
                                    ->unique()
                                    ->required()
                                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state)),
                                Select::make('status')
                                    ->label(__('fields.status'))
                                    ->options([
                                        'active' => 'সচল',
                                        'inactive' => 'নিষ্ক্রিয়',
                                        'destroyed' => 'নষ্ট হয়ে গেছে',
                                        'replaced' => 'বদলানো হয়েছে',
                                    ])
                                    ->default('active')
                                    ->required(),
                                DatePicker::make('install_at')
                                    ->label(__('fields.install_at'))
                                    ->default(now())
                                    ->required(),
                                DatePicker::make(('uninstall_at'))
                                    ->label('অপসারণের তারিখ')
                                    ->default(null)
                                    ->placeholder('যদি থাকে')
                                    ->nullable(),
                                TextInput::make('current_reading')
                                    ->label(__('fields.current_reading'))
                                    ->formatStateUsing(fn($state)=>ElectricBillHelper::en2bn($state))
                                    ->dehydrateStateUsing(fn($state)=>ElectricBillHelper::bn2en($state)),
                                RichEditor::make('remarks')
                                    ->label('মন্তব্য')
                                    ->placeholder('এখানে নোট লিখুন...')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('images/e_remarks')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn (mixed $state): string => (string) $state),
                            ])
                            ->maxItems(function ($state) {
                                return collect($state)->where('status', 'active')->count() > 0 ? 
                                collect($state)->count() : collect($state)->count()+1;
                            })
                            ->columns(5)
                            
                    ])->columnSpanFull(),
            ])->columns(12);
    }
}
