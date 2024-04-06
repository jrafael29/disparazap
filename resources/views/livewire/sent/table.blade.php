<div wire:poll.5s>
    @if(count($sents))
    <x-table :headers="$headers" :rows="$sents" wire:model="expanded" expandable>



        @scope("cell_start_at", $sent)
        {{$sent->start_at->diffForHumans()}}
        @endscope
        @scope("cell_created_at", $sent)
        {{$sent->created_at->diffForHumans()}}
        @endscope

        @scope('expansion', $sent)
        @php
        $flowToSentCount = \App\Models\FlowToSent::where('sent_id', $sent->id)->count();
        $doneFlowToSentCount = \App\Models\FlowToSent::where('sent_id', $sent->id)->where('sent', 1)->count();
        $instancesById = \App\Models\FlowToSent::with('instance')
        ->select('instance_id')
        ->where('sent_id', $sent->id)
        ->groupBy('instance_id')->get();

        $endsAt = 0;
        $sentHasEnd = $flowToSentCount == $doneFlowToSentCount;

        $lastFlow = $sent->flows->last();
        if($lastFlow->sent){
        $endsAt = $lastFlow->updated_at;
        }else{
        $endsAt = $lastFlow->to_sent_at;
        }

        @endphp
        <div class="bg-base-200 p-8 ">
            <p> <span class="font-bold">Inicio:</span> {{$sent->created_at->format('d/m/Y H:i')}}</p>
            <p> <span class="font-bold">Termino:</span> {{$endsAt->format('d/m/Y H:i')}}</p>
            <br />
            @if(count($sent->flows))
            <p> <span class="font-bold">Nome do fluxo enviado:</span> {{$sent->flows[0]->flow->description}}</p>
            @endif
            <br />
            <p> <span class="font-bold">Progresso:</span></p>
            <x-progress value="{{$doneFlowToSentCount}}" max="{{$flowToSentCount}}"
                class=" {{$doneFlowToSentCount === $flowToSentCount ? 'progress-success' : 'progress-info'}}  h-2" />

            <div class="flex gap-5 flwx-wrap my-2">
                <x-stat value="{{$flowToSentCount}}" title="Total"
                    description="Quantidade total de fluxos a serem enviados" color="text-blue-500"
                    icon="o-paper-airplane" tooltip="Quantidade total de fluxos" />
                <x-stat value="{{$doneFlowToSentCount}}" title="Enviado" description="Fluxo de mensagens enviados"
                    color="text-green-500" icon="o-check" tooltip="Quantidade de fluxos enviados" />
                <x-stat value="{{($flowToSentCount - $doneFlowToSentCount)}}" title="Na fila"
                    description="Fluxo de mensagens em espera" color="text-gray-500" icon="o-clock"
                    tooltip="Quantidade de fluxos em espera" />
            </div>

            <br />

            <div class="flex justify-end">

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