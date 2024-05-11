<div>
    <!-- HEADER -->
    <x-header title="Instâncias do WhatsApp" subtitle="Crie e gerencie suas instancias." separator progress-indicator>
        <x-slot:actions>
            <x-button tooltip-left="Nova instancia do whatsapp" icon="o-plus" spinner class="btn-primary"
                @click="$wire.openCreateModal = true" />
        </x-slot:actions>
    </x-header>
    <div>
        <x-modal wire:model="openCreateModal" title="Nova instancia" subtitle="Cadastre uma nova instancia" separator>
            <livewire:instance.form />
        </x-modal>
    </div>
    {{-- Success is as dangerous as failure. --}}
    <div>
        <livewire:instance.table />
    </div>


</div>