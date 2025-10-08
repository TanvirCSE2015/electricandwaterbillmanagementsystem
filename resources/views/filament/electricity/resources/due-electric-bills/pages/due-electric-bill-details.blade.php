<x-filament-panels::page>
    {{-- Page content --}}
    {{ $this->form }}
    @php
        $data=DB::table('electric_bills')->where('customer_id', $this->record->id)->where('is_paid', false)
        ->orderBy('id', 'asc')->limit($this->count)->get();

    @endphp
    
    {{ $this->table }}
    <div style="width:100%;display:flex;justify-content:flex-end;margin-top: -25px;padding-right: 3.5rem;">
        <span style="font-weight:bold;">
            মোট বকেয়া: {{ $this->en2bn($data->sum('total_amount')) }} /= 
        </span>
    </div>
</x-filament-panels::page>
