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
    @if($onlineInstances > 0)
    <livewire:flow.sent.steps :flow="$flow" />
    @else
    <div>
        <p class="text-3xl mb-5">Você não tem nenhuma instancia conectada</p>
    </div>
    @endif
    <livewire:flow.sent.table :flow="$flow" />
</div>