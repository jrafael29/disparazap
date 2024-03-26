<div>
    <!-- HEADER -->
    <x-header title="Gerenciar contatos" subtitle="Extraia telefones de algum texto, para usar no disparo." separator
        progress-indicator>
    </x-header>

    <!-- TABLE  -->
    <div>
        <div class="mb-3">
            <x-select wire:change='orderContacts' label="Filtrar por DDD" icon="o-globe-americas"
                hint="Digite o numero para buscar mais rapido" :options="$ddds" wire:model="dddSelected" />
        </div>
        <div class="h-96 overflow-y-auto">
            <x-table selectable wire:model='selectedContacts' :headers="$headers" :rows="$contacts" striped
                @row-selection="console.log($event.detail)">

                @scope('actions', $contact)
                <x-button spinner icon="o-trash" wire:click="delete({{ $contact->id }})" spinner class="btn-sm" />
                @endscope
            </x-table>

        </div>
        <div>

            <x-button label="Excluir selecionados" icon="o-trash" wire:click="deleteSelectedContacts" spinner />
            <x-button label="Enviar para selecionados" icon="o-check" wire:click="save" spinner />

        </div>
    </div>
</div>