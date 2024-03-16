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
                <x-alert title="Conecte uma instancia ao WhatsApp para agendar um disparo" icon="o-exclamation-triangle"
                    class="alert-warning" shadow>

                    <x-slot:actions>
                        <a wire:navigate href="{{route('instance')}}">
                            <x-button label="Conectar uma instancia" />
                        </a>
                    </x-slot:actions>
                </x-alert>

            </p>
        </span>

    </div>
    @endif
    <livewire:flow.sent.table :flow="$flow" />
</div>