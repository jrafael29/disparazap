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
    <div class="mb-20">
        <span>
            <p>
                <span class="text-3xl">
                    Nenhuma instância
                    <span class="text-green-500">conectada</span> no WhatsApp
                    foi encontrada.
                </span>

                <span class="text-blue-600"> <a href="{{route('instance')}}">Conectar uma instancia</a> </span>
            </p>
        </span>

    </div>
    @endif
    <livewire:flow.sent.table :flow="$flow" />
</div>