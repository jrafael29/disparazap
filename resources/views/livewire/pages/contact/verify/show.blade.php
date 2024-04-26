
<div>
    <div class="mb-3">
        <a wire:navigate href="{{route('verify')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>
    <x-header title="Verificação de existência"
        subtitle=""
        separator progress-indicator>
    </x-header>


<div wire:poll.4s wire:key='expand-{{$verify->id}}' class="bg-base-200 p-8 ">
    @php 
        $verifiedPhonenumbers = $verify->verifies;

        $percentageVerified = 0;
        $percentageVerifiedAvailable = 0;
        $countVerifiedOnWhatsapp = $verify->verifies->where('verified', 1)
            ->where('isOnWhatsapp', 1)
            ->count();
        $countVerifiedNotOnWhatsapp = $verify->verifies->where('verified', 1)
            ->where('isOnWhatsapp', 0)
            ->count();
        $countVerified = $verify->verifies->where('verified', 1)->count();
        $countTotal = $verify->verifies->count();
        // $countUnavailable = $countVerified - $countVerifiedOnWhatsapp;
        
        if($countVerifiedOnWhatsapp > 0 && $countVerified > 0){
            $percentageVerified = ($countVerified  / $countTotal) * 100;
            $percentageVerifiedAvailable = ($countVerifiedOnWhatsapp  / $countVerified) * 100;
        }

    @endphp

    <div>
        @if($verify->done)
        <div wire:key='{{$verify->id}}'>
            <div class="mb-3">
                @if($this->showGroups)
                <x-button wire:click='toggleShowGroups()' spinner class="btn-outline" label="Ocultar grupos"/>
                <div>
                    <livewire:contact.verify.select-group verifyId="{{$verify->id}}" showGroups="{{$this->showGroups}}" wire:key='{{$verify->id}}'/>
                </div>
                @else
                <x-button wire:click='toggleShowGroups()' spinner class="btn-primary" label="Adicionar contatos a um grupo"/>
                @endif
            </div>
        </div>
       
        @endif
    </div>

    <div>
        @if($verify->done)
            <x-progress 
                value="{{$verify->verifies->where('verified', 1)->count()}}" 
                max="{{$verify->verifies->count()}}" 
                class="progress-success h-3"
            />
        @else
            <x-progress 
                value="{{$verify->verifies->where('verified', 1)->count()}}" 
                max="{{$verify->verifies->count()}}" 
                class="progress-primary h-3"
            />
        @endif
        <div class=" flex justify-center ">
            <div class="flex justify-center items-start gap-10 pt-10">
                <div class="flex flex-col-reverse gap-10">
                    <div class="flex justify-center">
                        @if($verify->done)
                        <x-progress-radial class="text-success" value="{{ $percentageVerified }}" style="--size:6rem; --thickness: 2px" />
                        @else
                        <x-progress-radial class="text-info" value="{{ $percentageVerified }}" style="--size:6rem; --thickness: 2px" />
                        @endif
                    </div>
                    <div class="flex flex-col-reverse justify-start items-start gap-2">
                        <div class="text-wrap flex items-center justify-center gap-2">
                            <div class="">
                                <x-icon name="o-shield-check" class="w-9 h-9 text-green-500" />
                            </div>
                            <div class="">
                                <p class="text-2xl">Números Verificados</p>
                                <p class="text-1xl">{{$verify->verifies->where('verified', 1)->count()}}</p>
                            </div>
                        </div>
                        <div class="text-wrap flex items-center justify-center gap-2">
                            <div class="">
                                <x-icon name="o-device-phone-mobile" class="w-9 h-9 text-green-500" />
                            </div>
                            <div class="">
                                <p class="text-2xl">Números Totais</p>
                                <p class="text-1xl">{{$verify->verifies->count()}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" flex flex-col-reverse gap-10 ">
                    <div class="flex justify-center">
                        <small>{{(int)$percentageVerifiedAvailable}}% dos números verificados estão no WhatsApp.</small>
                    </div>
                    <div class="flex justify-center">
                        @if($verify->done)
                            <x-progress-radial value="{{ $percentageVerifiedAvailable }}" class="text-success" style="--size:6rem; --thickness: 2px" />
                        @else
                        <x-progress-radial value="{{ $percentageVerifiedAvailable }}" class="text-info" style="--size:6rem; --thickness: 2px" />
                        @endif
                    </div>
                    <div class="flex flex-col-reverse items-start gap-2">
                        <div class="text-wrap flex items-center justify-center gap-2">
                            <div class="">
                                <x-icon name="o-check-badge" class="w-9 h-9 text-green-500" />
                            </div>
                            <div class="">
                                <p class="text-2xl text-wrap">Números Existentes</p>
                                <p class="text-1xl">{{$countVerifiedOnWhatsapp}}</p>
                            </div>
                        </div>
                        
                        <div class="text-wrap flex items-center justify-center gap-2">
                            <div class="">
                                <x-icon name="o-x-mark" class="w-9 h-9 text-red-500" />
                            </div>
                            <div class="">
                                <p class="text-2xl text-wrap">Números Inexistentes</p>
                                <p class="text-1xl">{{$countVerifiedNotOnWhatsapp}}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>