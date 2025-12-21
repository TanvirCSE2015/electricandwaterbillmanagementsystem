@php
// use App\Helpers\ElectricBillHelper;
use Rakibhstu\Banglanumber\NumberToBangla;

$numto = new NumberToBangla();
$previousDue =  $this->getTableQuery()->get()->sum('payable_amount');
@endphp
<x-filament-panels::page>
    {{-- Page content --}}
    {{ $this->form }}
    {{ $this->table }}

    <div style="width:100%;display:flex;justify-content:flex-end;margin-top: -25px;padding-right: 3.5rem; color: red;">
        <span style="font-weight:bold;">
            পূর্বের বকেয়া: {{ $numto->bnCommaLakh($this->record?->previous_due ?? 0) }} /= 
        </span>
    </div>
    <div style="width:100%;display:flex;justify-content:flex-end;margin-top: -25px;padding-right: 3.5rem;">
        <span style="font-weight:bold;">
            মোট বকেয়া: {{ $numto->bnCommaLakh($previousDue + $this->record?->previous_due ?? 0) }} /= 
        </span>
    </div>
</x-filament-panels::page>
