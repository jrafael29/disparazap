<div wire:poll>
    <x-card title="{!!$instance->description!!}" subtitle='{{$instance->online ? "Conectado": "Desconectado" }}'>

        <div class="mb-3 text-wrap">
            @if($instance->online && !empty($profilePictureUrl))
            <x-slot:figure>
                <img class="rounded-full" width="150" src="{{$profilePictureUrl}}" />
            </x-slot:figure>
            @endif
            <span>
                @if($instance->online)
                @if($profileName)
                <p>Nome: <b>{{$profileName}}</b></p>
                @endif
                @if($profileStatus)
                <p class="w-80">Status: <b>{{$profileStatus}}</b></p>
                @endif
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
            @if($instance->online)
            <x-button spinner wire:click='logoutInstanceClick' spinner icon="o-power" label="Deslogar Instancia"
                class="btn-error" />
            @else
            {{--
            <x-button spinner wire:click='deleteInstanceClick' spinner icon="o-trash" label="Remover Instancia"
                class="btn-error" /> --}}

            <x-button spinner wire:click='getQrCodeClick' icon="o-qr-code"
                label="{{$instance->qrcode_path ? 'Atualizar' : 'Buscar'}} QRCode" class="btn-outline" />

            @endif
        </div>
    </x-card>
</div>