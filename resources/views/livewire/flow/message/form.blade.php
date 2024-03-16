<div class="">
    <div class="mb-3">
        <span>
            <p>Escolha um tipo de mensagem</p>
        </span>
    </div>
    <div class="mb-3">
        @foreach($types as $type)
        @if($messageTypeSelected === $type['name'])
        <x-button class="btn-primary mb-3" type="button" label="{{ ucfirst($type['description']) }}"
            wire:click="changeTypeSelected('{{ $type['name'] }}')" />


        @else
        <x-button class="btn-outline mb-3" type="button" label="{{ ucfirst($type['description']) }}"
            wire:click="changeTypeSelected('{{ $type['name'] }}')" />

        @endif
        @endforeach
    </div>


    <x-form class="" wire:submit='handleSubmit'>

        @switch($messageTypeSelected)

        @case('text')
        <div class="mb-3">
            <x-textarea max=500 label="Conteudo" wire:model="text" placeholder="Digite o conteudo da mensagem"
                hint="O texto da mensagem" rows="1" inline />
        </div>
        @break

        @case('image')
        <x-input label="Texto de apoio da imagem" wire:model="text" hint="Mensagem que será enviada junto à imagem" />
        <x-file wire:model="image" label="Imagem" hint="Somente imagens" accept="image/*" />
        @break

        @case('video')
        <x-input label="Texto de apoio do vídeo" wire:model="text" hint="Mensagem que será enviada junto ao video" />
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

            <x-input label="Tempo de digitação" min="1" max="29" type="number" wire:model="delay"
                hint="Tempo (digitando...) em segundos" />

        </div>
        <x-slot:actions>
            <div>
                <x-button type="submit" class="btn-primary" label="Adicionar Mensagem" />
            </div>
        </x-slot:actions>
    </x-form>
</div>