<div>
    <!-- HEADER -->
    <x-header title="Fluxo de mensagens" separator progress-indicator>

        <x-slot:actions >
            <x-button tooltip-left="Novo fluxo" icon="o-plus" spinner class="btn-primary"
                @click="$wire.openModal = true" />
            <x-modal wire:model="openModal" title="Novo fluxo de mensagens" subtitle="Cadastre um novo fluxo de mensagens" separator>
                <livewire:flow.form />
            </x-modal>
        </x-slot:actions>
    </x-header>
    {{-- Care about people's approval and you will be their prisoner. --}}

    <div class="mb-3">
        
    </div>
    <div class="mb-3">
        <livewire:flow.table />
    </div>
</div>