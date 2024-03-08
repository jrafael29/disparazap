<div class="my-16">
    <div class="mb-5">
        @foreach($types as $type)
        @if($messageTypeSelected === $type['name'])
        <x-button class="btn-primary" type="button" label="{{ ucfirst($type['name']) }}"
            wire:click="changeTypeSelected('{{ $type['name'] }}')" />


        @else
        <x-button class="btn-outline" type="button" label="{{ ucfirst($type['name']) }}"
            wire:click="changeTypeSelected('{{ $type['name'] }}')" />

        @endif
        @endforeach
    </div>


    <x-form class="text-white" wire:submit='handleSubmit'>

        @switch($messageTypeSelected)

        @case('text')
        <div>
            <x-textarea max=500 label="Conteudo" wire:model="text" placeholder="Digite o conteudo da mensagem"
                hint="Maximo de 500 caractere" rows="1" inline />
        </div>
        @break

        @case('image')
        <x-input label="Texto de Apoio" wire:model="text" hint="Mensagem que será enviada junto à imagem" />
        <x-file wire:model="image" label="Imagem" hint="Somente imagens" accept="image/*" />
        @break

        @case('video')
        <x-input label="Texto de Apoio" wire:model="text" hint="Mensagem que será enviada junto ao video" />
        <x-file wire:model="video" label="Video" hint="Somente videos" accept="video/*" />
        @break

        @case('audio')
        <x-input label="Texto de Apoio" wire:model="text" hint="Mensagem que será enviada junto ao àudio" />
        <x-file wire:model="file" label="Audio" hint="Somente audios" accept="audio/*" />
        @break

        @case('sticky')
        <x-file wire:model="image" label="Imagem da figurinha" hint="Somente imagens" accept="image/*" />
        @break

        @endswitch

        <div>

            <x-input label="Tempo de espera" min="1" max="5" type="number" wire:model="delay"
                hint="Tempo (digitando...) em segundos" />

        </div>

        <div>
            <x-button class="btn-primary" label="Adicionar Mensagem" />
        </div>

    </x-form>
</div>