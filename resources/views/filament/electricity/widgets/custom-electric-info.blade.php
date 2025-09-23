<x-filament-widgets::widget>
    
    <div wire:snapshot="{&quot;data&quot;:[],&quot;memo&quot;:{&quot;id&quot;:&quot;eHe0w4dAVrZHV8M4AjZ5&quot;,&quot;name&quot;:&quot;filament.widgets.account-widget&quot;,&quot;path&quot;:&quot;water&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;bn&quot;},&quot;checksum&quot;:&quot;e905c8a6142b0105d8229c11c7c4575afacade82dc33d0086d8a386936cc36e4&quot;}" wire:effects="{&quot;partials&quot;:[]}" wire:id="eHe0w4dAVrZHV8M4AjZ5" class="fi-wi-widget fi-grid-col lg:fi-grid-col-span fi-account-widget" style="--col-span-lg: span 1 / span 1;">
        <section x-data="{
            isCollapsed:  false ,
            }" class="fi-section">
        
            <div class="fi-section-content-ctn">
                <div class="fi-section-content">
                    <img class="fi-avatar fi-circular fi-size-lg fi-user-avatar" src="{{ asset('images/bulb.png') }}" alt="Avatar of Md Tanvir" loading="lazy">

                    <div class="fi-account-widget-main">
                        <h2 class="fi-account-widget-heading">
                            বিদ্যুৎ বিল ম্যানেজমেন্ট সিস্টেম
                        </h2>

                        <h4 class="fi-account-widget-user-name" style="margin-left: 2.5rem; color: rgb(230, 143, 13); font-weight: 600;">
                        ঢাকা ক্যান্টনমেন্ট বোর্ড
                        </h4>
                    </div>

                    {{-- <form action="http://127.0.0.1:8000/water/logout" method="post" class="fi-account-widget-logout-form">
                        <input type="hidden" name="_token" value="6YyGge52brVWS4fTDr7vvN4i0s1MoFQRYxyl2GUb" autocomplete="off">
                        <button class="fi-icon-btn fi-size-md" title="সাইন আউট" aria-label="সাইন আউট" type="submit" wire:loading.attr="disabled">
                            <svg class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"></path>
                                <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <button class="fi-btn fi-size-md fi-labeled-from-sm" type="submit" wire:loading.attr="disabled">
                            <svg class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"></path>
                                <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd"></path>
                            </svg>          
                            সাইন আউট
                        </button>
                    </form> --}}
                </div>
            
            </div>
        </section>
    </div>
   
</x-filament-widgets::widget>
