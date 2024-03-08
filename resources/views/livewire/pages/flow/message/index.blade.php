<div>
    <x-header title="Gerenciamento de mensagens" subtitle="Fluxo tal" separator progress-indicator>
    </x-header>

    <div class="mb-3">
        <livewire:flow.message.form :flow="$flow" />
    </div>
    <div>
        <livewire:flow.message.table :flow="$flow" />
    </div>
</div>