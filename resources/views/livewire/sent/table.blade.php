<div wire:poll.5s>
    @if(count($sents))
    <x-table :headers="$headers" :rows="$sents" wire:model="expanded" expandable>

        @scope('expansion', $sent)
        @php
        $flowToSentCount = \App\Models\FlowToSent::where('sent_id', $sent->id)->count();
        $doneFlowToSentCount = \App\Models\FlowToSent::where('sent_id', $sent->id)->where('sent', 1)->count();
        $instancesById = \App\Models\FlowToSent::with('instance')
        ->select('instance_id')
        ->where('sent_id', $sent->id)
        ->groupBy('instance_id')->get();

        @endphp
        <div class="bg-base-200 p-8 ">
            <p> <span class="font-bold">Inicio:</span> {{$sent->created_at->diffForHumans()}}</p>
            <br />
            @if(count($sent->flows))
            <p> <span class="font-bold">Nome do fluxo enviado:</span> {{$sent->flows[0]->flow->description}}</p>
            @endif
            <br />
            <p> <span class="font-bold">Progresso:</span></p>
            <x-progress value="{{$doneFlowToSentCount}}" max="{{$flowToSentCount}}"
                class=" {{$doneFlowToSentCount === $flowToSentCount ? 'progress-success' : 'progress-info'}}  h-2" />

            <div class="flex gap-5 flwx-wrap my-2">
                <x-stat value="{{$doneFlowToSentCount}}" description="Fluxos enviados" color="text-green-500"
                    icon="o-check" tooltip="Fluxos enviados" />
                <x-stat value="{{($flowToSentCount - $doneFlowToSentCount)}}" description="Fluxos em espera"
                    color="text-gray-500" icon="o-clock" tooltip="Fluxos em espera" />
            </div>

            <br />

            <div>

                @if($sent->paused)
                <x-button spinner wire:click='playSent({{$sent->id}})' spinner icon="o-play-circle"
                    label="Continuar envio" class="btn-success" />
                @else
                <x-button spinner wire:click='pauseSent({{$sent->id}})' spinner icon="o-pause-circle"
                    label="Pausar envio" class="btn-warning" />
                @endif

            </div>

            {{-- <p><span class="font-bold">Instancias utilizadas:</span></p>
            <div>
                <ul>
                    @foreach($instancesById as $instanceId)
                    <li>{{$instanceId->instance->description}}</li>
                    @endforeach
                </ul>
            </div> --}}

        </div>
        @endscope

    </x-table>
    @else
    <div class="w-full">
        <x-alert title="Ops... Nenhum envio encontrado." icon="o-exclamation-triangle" class="alert-warning">
        </x-alert>
    </div>
    @endif
</div>