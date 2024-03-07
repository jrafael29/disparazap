<div>
    <x-card subtitle="Campos em vermelho estão desconectados">

        <x-table :headers="$headers" :rows="$instances" :row-decoration="$rowDecoration" :sort-by="$sortBy">
            <div>
                @scope('cell_name', $instance)
                {{ $instance}}
                @endscope
                @scope('actions', $instance)


                <div>

                    {{-- modal view --}}

                    <x-modal :id="'modal_view_' . $instance->id"
                        :title="'Detalhes da instancia: ' . $instance->description"
                        subtitle='Status: {{$instance->online ? " Conectado" : "Desconectado" }}'>
                        @if ( !empty($instance->qrcode_path))
                        <!-- Verifique se há um novo caminho do QR Code -->
                        <div class="flex justify-center">
                            <img src="{{ asset('storage/' . $instance->qrcode_path) }}"
                                alt="Qr code instancia {{ $instance->description }}">
                        </div>
                        @elseif($this->updatedQrCodePath)
                        <div class="flex justify-center">
                            <img src="{{ asset('storage/' . $this->updatedQrCodePath) }}"
                                alt="Qr code instancia {{ $instance->description }}">
                            @else
                            <h1 class="text-2xl">Solicite um QRCode</h1>
                            @endif

                            <x-slot name="actions">
                                <x-button label="Fechar"
                                    onclick="document.getElementById('modal_view_{{ $instance->id }}').close()" />
                                @if ($this->updatedQrCodePath)
                                <x-button wire:click="getQrClick({{ $instance->id }})" label="Atualizar QRCode"
                                    class="btn-success" />
                                @else
                                <x-button wire:click="getQrClick({{ $instance->id }})" label="Solicitar QRCode"
                                    class="btn-primary" />
                                @endif
                            </x-slot>
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