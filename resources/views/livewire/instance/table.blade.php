<div>
    <x-card>

        <x-table :headers="$headers" :rows="$instances" :sort-by="$sortBy">
            <div>
                @scope('actions', $instance)


                <div>

                    {{-- modal view --}}

                    <x-modal :id="'modal_view_' . $instance->id"
                        :title="'Detalhes da instancia: ' . $instance->description">

                        @if($instance->qrcode_path)
                        <div class="flex justify-center">
                            <img src="{{asset('storage/'.$instance->qrcode_path)}}"
                                alt="Qr code instancia {{$instance->description}}">

                        </div>
                        @else
                        <h1 class="text-2xl">Solicite um QRCode</h1>
                        @endif

                        <x-slot:actions>
                            {{-- Notice `onclick` is HTML --}}
                            <x-button label="Fechar"
                                onclick="document.getElementById('modal_view_{{ $instance->id }}').close()" />
                            @if(asset('storage/'.$instance->qrcode_path))
                            <x-button wire:click='updateQrClick' label="Atualizar QRCode" class="btn-success" />
                            @else
                            <x-button wire:click='getQrClick' label="Solicitar QRCode" class="btn-primary" />
                            @endif
                        </x-slot:actions>
                    </x-modal>

                    {{-- modal delete --}}

                    <x-modal :id="'modal_exclude_' . $instance->id"
                        :title="'Confirmar exclusão da instancia: ' . $instance->description . '?'">
                        <div>Clique em "cancelar" ou aperte ESC para sair.</div>
                        <x-slot:actions>
                            {{-- Notice `onclick` is HTML --}}
                            <x-button label="Cancelar"
                                onclick="document.getElementById('modal_exclude_{{ $instance->id }}').close()" />

                            <x-button label="Confirmar" class="btn-error"
                                wire:click='deleteInstanceClick({{$instance->id}})' spinner />


                        </x-slot:actions>
                    </x-modal>
                </div>


                <div class="flex">

                    <x-button icon="c-arrows-pointing-out"
                        onclick="document.getElementById('modal_view_{{ $instance->id }}').showModal()" spinner
                        class="btn-ghost btn-sm " />

                    <x-button icon="o-trash"
                        onclick="document.getElementById('modal_exclude_{{ $instance->id }}').showModal()" spinner
                        class="btn-ghost btn-sm text-red-500" />
                </div>
                @endscope

            </div>
        </x-table>


    </x-card>
</div>