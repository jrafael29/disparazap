<div>
    <div class="mb-3">
        <a wire:navigate href="{{URL::to('/message-flow')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>

    <x-header title="Gerenciamento de mensagens"
        subtitle="Aqui você pode criar um fluxo de mensagens (texto, imagem, video). Organize a ordem deu seu jeito."
        separator progress-indicator>
    </x-header>
    <div class="mb-3">
        <livewire:flow.message.form :flow="$flow" />
    </div>
    <div>
        <livewire:flow.message.table :flow="$flow" />
    </div>
</div>