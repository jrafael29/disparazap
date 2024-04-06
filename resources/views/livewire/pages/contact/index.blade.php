<div>
    <!-- HEADER -->
    <x-header title="Gerenciar contatos" subtitle="Extraia telefones de algum texto, para usar no disparo." separator
        progress-indicator>
        <x-slot:actions>
            <a wire:navigate href="{{route('contact.import')}}">
                <x-button tooltip-left="Importar contatos" icon="o-arrow-down-tray" spinner class="btn-outline" />
            </a>

            <a wire:navigate href="{{route('contact.create')}}">
                <x-button tooltip-left="Novo contato" icon="o-plus" spinner class="btn-outline" />
            </a>

        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <div class="">
        <div class="mb-3">
            <x-select wire:change='orderContacts' label="Filtrar por DDD" icon="o-globe-americas"
                hint="Digite o numero para buscar mais rapido" :options="$ddds" wire:model="dddSelected" />
        </div>
        <div class="h-96 overflow-y-auto">
            <x-table selectable wire:model='selectedContacts' :headers="$headers" :rows="$contacts" striped
                @row-selection="$wire.updateSelectedContacts" @row-selection="console.log('eae')">


                @scope('actions', $contact)
                <x-button spinner icon="o-trash" wire:click="delete({{ $contact->id }})" spinner class="btn-sm" />
                @endscope
            </x-table>

        </div>
        <div>

            <x-button label="Excluir selecionados" icon="o-trash" wire:click="deleteSelectedContacts" spinner />
            <x-button label="Enviar para selecionados" icon="o-check" spinner />

        </div>
    </div>
</div>