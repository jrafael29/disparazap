<div>
    <x-header title="Importar contato" subtitle="Importe contatos personalizado." separator progress-indicator>
    </x-header>
    <div class="mb-3">
        <a wire:navigate href="{{route('contact')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>

    <div class="">
        <p class="mb-2">De onde você quer importar os contatos?</p>

        @foreach($importOptions as $key => $option)
        @if($key === $importOption)
        <x-button spinner class="btn-primary" wire:key='{{$key}}' wire:click="changeImportOption('{{$key}}')">
            {{$option}}
        </x-button>
        @else
        <x-button spinner wire:key='{{$key}}' class="btn-outline" wire:click="changeImportOption('{{$key}}')">
            {{$option}}
        </x-button>
        @endif
        @endforeach


        @switch($importOption)
        @case('raw-text')
        <div class="my-3">
            <livewire:contact.import.raw-text />
        </div>
        @break
        @endswitch

    </div>
    {{-- The whole world belongs to you. --}}
</div>