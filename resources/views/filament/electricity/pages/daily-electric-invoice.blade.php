@php
use Rakibhstu\Banglanumber\NumberToBangla;

$numto = new NumberToBangla();
@endphp
<x-filament-panels::page>
    {{-- Page content --}}
    {{ $this->form }}
    <div style="width:100%;display:flex;justify-content:flex-end;margin-top: -25px;">
        <span style="font-weight:bold;">
            মোট আদায়: {{ $this->getTotalAmount() }} 
        </span>
    </div>
    {{ $this->table }}
    
</x-filament-panels::page>
