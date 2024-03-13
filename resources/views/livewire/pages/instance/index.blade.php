<div>
    <!-- HEADER -->
    <x-header title="Instâncias do WhatsApp" subtitle="Crie e gerencie suas instancias." separator progress-indicator>
    </x-header>
    {{-- Success is as dangerous as failure. --}}
    <div class="mb-5">
        <livewire:instance.form />
    </div>
    <div>
        <livewire:instance.table />
    </div>


</div>