<div wire:poll>
    <x-card class="text-wrap" title="{!!$instance->description!!}"
        subtitle='{{$instance->online ? "Conectado": "Desconectado" }}'>

        <div class="mb-3">
            @if($instance->online && !empty($profilePictureUrl))
            <x-slot:figure>
                <img class="rounded-full" width="150" src="{{$profilePictureUrl}}" />
            </x-slot:figure>
            @endif
            <span>
                @if($instance->online && !empty($profilePictureUrl))
                <p>Nome: <b>{{$profileName}}</b></p>
                <p>Status: <b>{{$profileStatus}}</b></p>
                @endif
                <p>Numero: <b>{{$instance->phonenumber}}</b></p>

            </span>
        </div>

        <div class="mb-3">
            {{-- Success is as dangerous as failure. --}}
            @if(!$instance->online)
            @if($test)
            <img src="{{ asset('storage/'.$test) }}" alt="QR Code">
            @endif
            @else
            <div class="text-center">
                <p class="text-2xl"><b>Instancia Conectada.</b></p>
                <p>Agora você pode utiliza-la para enviar mensagens.</p>
            </div>
            @endif
        </div>
        <div class="flex justify-center gap-3">
            <x-button spinner wire:click='deleteInstanceClick' spinner icon="o-trash"
                label="{{!$instance->online ? 'Remover' : 'Deslogar'}} Instancia" class="btn-error" />
            @if(!$instance->online)

            <x-button spinner wire:click='getQrCodeClick' icon="o-qr-code" label="Buscar QRCode" class="btn-outline" />
            @endif
        </div>
    </x-card>
</div>