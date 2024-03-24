<div>
    <!-- HEADER -->
    <x-header title="Gerenciar contatos" subtitle="Extraia telefones de algum texto, para usar no disparo." separator
        progress-indicator>
    </x-header>

    <!-- TABLE  -->
    <div>
        <div class="mb-3">
            <x-select wire:change='teste' label="Filtrar por DDD" icon="o-user"
                hint="Digite o numero para buscar mais rapido" :options="$ddds" wire:model="selectedUser" />
        </div>
        <x-table :headers="$headers" :rows="$contacts" striped @row-click="alert($event.detail.name)" />
    </div>
</div>