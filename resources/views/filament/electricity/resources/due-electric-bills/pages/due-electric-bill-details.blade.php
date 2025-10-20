@php
use App\Helpers\ElectricBillHelper;
use Rakibhstu\Banglanumber\NumberToBangla;

$numto = new NumberToBangla();
@endphp
<x-filament-panels::page>
    {{-- Page content --}}
    {{ $this->form }}
    @php
        $data=DB::table('electric_bills')->where('customer_id', $this->record->id)->where('is_paid', false)
        ->orderBy('id', 'asc')->limit($this->count)->get();
        $dueTotal=0;
        foreach ($data as $bill) {
            if ($bill->surcharge > 0) {
                    $dueTotal += $bill->total_amount;
                    continue;
            }else{
                $surcharge= ElectricBillHelper::calculateSurcharge($bill);
                    $dueTotal += $bill->total_amount + $surcharge;
            }
        }
    $previousDue = DB::table('previous_dues')->where(['customer_id' => $this->record->id,'is_paid' =>false])->first();
    @endphp
    
    {{ $this->table }}
    <div style="width:100%;display:flex;justify-content:flex-end;margin-top: -25px;padding-right: 3.5rem; color: red;">
        <span style="font-weight:bold;">
            পূর্বের বকেয়া: {{ $numto->bnCommaLakh(round($previousDue->amount ?? 0)) }} /= 
        </span>
    </div>
    <div style="width:100%;display:flex;justify-content:flex-end;margin-top: -25px;padding-right: 3.5rem;">
        <span style="font-weight:bold;">
            মোট বকেয়া: {{ $numto->bnCommaLakh(round($dueTotal + $previousDue?->amount ?? 0)) }} /= 
        </span>
    </div>
</x-filament-panels::page>
