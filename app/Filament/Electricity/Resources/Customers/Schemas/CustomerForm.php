<?php

namespace App\Filament\Electricity\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->columnSpan(6),
                TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->default(null)
                    ->columnSpan(6),
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
                Section::make('মিটার তথ্য')
                    ->schema([
                        Repeater::make('মিটার')
                            ->label('মিটার')
                            ->relationship('meters') 
                            ->schema([
                                TextInput::make('meter_number')
                                    ->label(__('fields.meter_number'))
                                    ->unique()
                                    ->required(),
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
                            ->columns(4)
                            
                    ])->columnSpanFull(),
            ])->columns(12);
    }
}
