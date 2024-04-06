<div>
    <div class="mb-3">
        <a wire:navigate href="{{route('flow')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>

    <x-header title="Gerenciamento de mensagens"
        subtitle="Aqui você pode gerenciar as mensagens do seu fluxo (texto, imagem, video). Organize a ordem do seu jeito."
        separator progress-indicator>
    </x-header>
    <div class="mb-3">
        <livewire:flow.message.form :flow="$flow" />
    </div>
    <div>
        <livewire:flow.message.table :flow="$flow" />
    </div>
</div>