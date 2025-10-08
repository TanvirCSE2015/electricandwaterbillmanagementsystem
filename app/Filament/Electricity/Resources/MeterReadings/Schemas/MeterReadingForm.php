<?php

namespace App\Filament\Electricity\Resources\MeterReadings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MeterReadingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('meter_id')
                ->label(__('fields.meter_id'))
                ->relationship('meter', 'meter_number')
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $lastReading = \App\Models\MeterReading::where('meter_id', $state)
                        ->latest('reading_date')
                        ->first();
                    $set('previous_reading', $lastReading?->current_reading ?? 0);
                    $set('consume_unit', null);
                }),

                DatePicker::make('reading_date')
                ->label(__('fields.reading_date'))
                ->default(now())->required()
                ->rule(function ($get) {
                    // Only run on create (no record yet)
                    if ($get('id')) {
                            return null;
                        }
                    $meterId = $get('meter_id');
                    $readingDate = $get('reading_date');
                    if (!$meterId || !$readingDate) {
                        return null;
                    }
                    $month = \Carbon\Carbon::parse($readingDate)->format('Y-m');
                    return function ($attribute, $value, $fail) use ($meterId, $month) {
                        $exists = \App\Models\MeterReading::where('meter_id', $meterId)
                            ->whereRaw("DATE_FORMAT(reading_date, '%Y-%m') = ?", [$month])
                            ->exists();
                        if ($exists) {
                            $fail('এই মিটারের জন্য এই মাসের রিডিং ইতিমধ্যে বিদ্যমান।');
                        }
                    };
                }),

                TextInput::make('previous_reading')
                ->label(__('fields.previous_reading'))
                ->disabled()
                ->dehydrated(true),
                TextInput::make('current_reading')
                ->label(__('fields.current_reading'))
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $set('consume_unit', $state - ($get('previous_reading') ?? 0));
                }),

                TextInput::make('consume_unit')
                ->label(__('fields.consume_unit'))
                ->disabled()
                ->dehydrated(true),
            ]);
    }
}
