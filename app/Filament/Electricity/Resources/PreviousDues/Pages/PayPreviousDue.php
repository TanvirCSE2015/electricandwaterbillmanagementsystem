<?php

namespace App\Filament\Electricity\Resources\PreviousDues\Pages;

use App\Filament\Electricity\Resources\PreviousDues\PreviousDueResource;
use App\Helpers\ElectricBillHelper;
use App\Models\ElectricBill;
use App\Models\PreviousDue;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;

class PayPreviousDue extends Page implements HasForms
{
    use InteractsWithRecord ,InteractsWithForms;

    protected static string $resource = PreviousDueResource::class;

    protected string $view = 'filament.electricity.resources.previous-dues.pages.pay-previous-due';

    public $data;
    public float $amount;
    public float $paid_amount;

    public function getTitle(): string
    {
        return 'পূর্বের বকেয়া পরিশোধ';
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->data=PreviousDue::where('id',$this->record->id)->with('customer')->get();
        $this->form->fill([
            'amount' => $this->record->amount,
            'paid_amount' => 0,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('গ্রাহকের তথ্য')
                    ->label('')
                    ->schema([
                         Grid::make(4)->schema([
                            TextEntry::make('customer.name')
                                ->label(__('fields.name'))
                                ->default($this->record->customer->name)
                                ->disabled(),
                            TextEntry::make('shop_no')
                                ->label(__('fields.shop_no'))
                                ->default($this->record->customer->shop_no)
                                ->disabled(),
                            TextEntry::make('meter_number')
                                ->label(__('fields.meter_number'))
                                ->default($this->record->customer->activeMeter->meter_number)
                                ->disabled(),
                            TextEntry::make('amount')
                                ->label(__('fields.total_amount'))
                                ->default($this->record->amount)
                                ->disabled(),
                            IconEntry::make('is_paid')
                                ->label(__('fields.is_paid'))
                                ->boolean()
                                ->default($this->record->is_paid)
                                ->disabled(),
                            TextEntry::make('remarks')
                            ->label(__('fields.remarks'))
                            ->default($this->record->remarks)
                            ->disabled(),
                        ]),
                    ])->columnSpanFull(),
            
                Section::make('Payment Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('amount')
                                ->label(__('fields.total_amount'))
                                ->disabled(),
                            TextInput::make('paid_amount')
                                ->label(__('fields.paid_amount'))
                                ->required()
                                ->numeric(),
                        ]),
                    ])->columnSpanFull(),

        ];
    }
    protected function getActions(): array
    {
        return [
            Action::make('pay_previous_due')
                ->label('পরিশোধ করুন')
                ->requiresConfirmation()
                ->action(function () {
                    $response = ElectricBillHelper::previousDueInvoice(
                        customerId: $this->record->customer_id,
                        userId: auth()->id(),
                        paidAmount: $this->form->getState()['paid_amount'],
                    );
                })
                ->color('success'),
        ];
    }
}
