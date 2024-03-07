<div wire:poll class="flex gap-5 flex-wrap">


    @foreach($instances as $instance)

    <x-card class="w-1/3 text-wrap" title="{{$instance->description}}"
        subtitle='{{$instance->online ? "Conectado": "Desconectado" }}'>
        <div class="mb-3">
            <span>
                <p>Numero: <b>{{$instance->phonenumber}}</b></p>
            </span>
        </div>

        <div class="mb-3">
            {{-- Success is as dangerous as failure. --}}
            @if(!$instance->online)
            @if($instance->qrcode_path)
            <img src="{{ asset('storage/'.$instance->qrcode_path) }}" alt="QR Code">
            @endif
            @else
            <div class="text-center">
                <p class="text-2xl"><b>Instancia Conectada.</b></p>
                <p>Agora você pode utiliza-la para enviar mensagens.</p>
            </div>
            @endif
        </div>
        <div class="flex justify-center gap-3">
            <x-button wire:click='deleteInstanceClick({{$instance->id}})' icon="o-trash" label="Buscar QRCode"
                class="btn-error" />
            <x-button icon="o-home" label="Buscar QRCode" class="btn-outline" />
        </div>
    </x-card>
    @endforeach


</div>