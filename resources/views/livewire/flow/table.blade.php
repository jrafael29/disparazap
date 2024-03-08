<div class="flex gap-5 flex-wrap">


    @foreach($flows as $flow)

    <div>
        <x-card class="text-wrap" title="{!!$flow->description!!}">
            <div class="flex flex-col gap-3">
                <div class="flex justify-center flex-wrap gap-3">
                    <x-button wire:click='deleteFlowClick({{$flow->id}})' spinner icon="o-trash" label=""
                        class="btn-error" />
                    <x-button icon="m-bars-arrow-up" Label="Gerenciar Envios" />
                    <a href="{{route('flow.message', ['flow' => $flow->id])}}">
                        <x-button icon="o-chat-bubble-bottom-center-text" Label="Gerenciar Mensagens" />
                    </a>
                </div>
            </div>
        </x-card>
    </div>
    @endforeach


</div>