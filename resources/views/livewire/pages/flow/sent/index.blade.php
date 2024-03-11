<div>
    <div class="mb-3">
        <a wire:navigate href="{{URL::to('/message-flow')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>
    <x-header title="Gerenciamento de envios"
        subtitle="Aqui você pode gerenciar o envio do fluxo. Escolha/informe números de telefones que receberão o fluxo."
        separator progress-indicator>
    </x-header>
    {{-- The best athlete wants his opponent at his best. --}}
    <livewire:flow.sent.steps :flow="$flow" />
</div>