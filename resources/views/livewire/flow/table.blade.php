<div class="flex gap-5 flex-wrap justify-center lg:justify-start">


    @forelse($flows as $flow)

    <div>
        <x-card class="text-wrap" title="{!!$flow->description!!}" subtitle="Gerencie o seu fluxo de mensagens">
            <x-slot:menu>
                <x-button tooltip-left="Excluir" icon="o-trash" class="btn-circle text-red-500 btn-sm ml-6"
                    wire:click='deleteFlowClick({{$flow->id}})' spinner label="" />
            </x-slot:menu>
            <div class="flex flex-col gap-3">
                <div class="flex flex-col items-center justify-center flex-wrap gap-3">
                    <div>
                        @if(count($flow->messages) > 0)
                        <a wire:navigate href="{{route('flow.sent', ['flow' => $flow->id])}}">
                            <x-button icon="m-bars-arrow-up" Label="Disparar fluxo" />
                        </a>
                        @else
                        <div>
                            <x-alert class="alert-warning" title="Adicione mensagens para envia-las"
                                icon="o-exclamation-triangle" shadow />

                        </div>
                        @endif
                    </div>
                    <div>
                        <a wire:navigate href="{{route('flow.message', ['flow' => $flow->id])}}">
                            <x-button icon="o-chat-bubble-bottom-center-text" Label="Gerenciar Mensagens" />
                        </a>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
    @empty
    <div class="w-full">
        <x-alert shadow title="Ops... Nenhum fluxo de mensagens encontrado."
            description="Crie um fluxo para adicionar mensagens e envia-las" icon="o-exclamation-triangle"
            class="alert-warning">
        </x-alert>
    </div>
    @endforelse


</div>