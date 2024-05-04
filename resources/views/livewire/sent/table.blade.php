<div wire:poll.10s>
    @if(count($sents))
    <x-table :headers="$headers" :rows="$sents" wire:model="expanded" striped expandable>

        @scope("cell_start_at", $sent)
        {{$sent->start_at->diffForHumans()}}
        @endscope
        @scope("cell_created_at", $sent)
        {{$sent->created_at->diffForHumans()}}
        @endscope

        @scope("cell_status", $sent)
        @if(!$sent->started && !$sent->done)
        <div>
            <x-button tooltip-right="Agendado" icon="o-clock" class="btn-sm" />
            <x-badge value="Agendado" class="badge-secondary" />
        </div>
        @elseif($sent->done)
        <div>
            <x-button tooltip-right="Finalizado" icon="o-check" class="btn-sm" />
            <x-badge value="Finalizado" class="badge-success" />
        </div>
        @elseif($sent->paused)
        <div>
            <x-button tooltip-right="Pausado" icon="o-play" class="btn-sm" />
            <x-badge value="Pausado" class="badge-warning" />
        </div>
        @else
        <div>
            <x-button tooltip-right="Em andamento" icon="o-pause" class="btn-sm" />
            <x-badge value="Em andamento" class="badge-primary" />
        </div>
        @endif
        @endscope

        @scope('expansion', $sent)

        @php
        $flowToSentCount = \App\Models\FlowToSent::where('sent_id', $sent->id)->count();
        $doneFlowToSentCount = \App\Models\FlowToSent::where('sent_id', $sent->id)->where('sent', 1)->count();
        $instancesById = \App\Models\FlowToSent::with('instance')
        ->select('instance_id')
        ->where('sent_id', $sent->id)
        ->groupBy('instance_id')->get();

        $endsAt = null;
        $sentHasEnd = $flowToSentCount == $doneFlowToSentCount;

        $lastFlowToSent = $sent->flows?->last();
        
        if($lastFlowToSent){
            if($lastFlowToSent?->sent){
                // foi enviado?
                $endsAt = $lastFlowToSent->updated_at;
            }else{
                $endsAt = $lastFlowToSent->to_sent_at;
            }
        }else{
        }

        @endphp
        <div class="bg-base- ">
            <p> <span class="font-bold">Inicio:</span> {{$sent->created_at->format('d/m/Y H:i')}}</p>
            <p> <span class="font-bold">Termino:</span> {{$endsAt?->format('d/m/Y H:i')}}</p>
            <br />
            @if(count($sent->flows))
            <div>
                <p> <span class="font-bold">Nome do fluxo enviado:</span> {{$sent->flows[0]->flow->description}}</p>
            </div>
            @endif
            <br />
            <div>
                <p> <span class="font-bold">Progresso:</span></p>
                <x-progress value="{{$doneFlowToSentCount}}" max="{{$flowToSentCount}}"
                    class=" {{$doneFlowToSentCount === $flowToSentCount ? 'progress-success' : 'progress-info'}}  h-2" />
            </div>
            <div class="lg:flex gap-10 my-2">
                <div class="text-wrap">
                    <x-stat value="{{$flowToSentCount}}" title="Total"
                        description="Quantidade total de fluxos a serem enviados" color="text-blue-500"
                        icon="o-paper-airplane" tooltip="Quantidade total de fluxos" />
                </div>
                <div>
                    <x-stat value="{{$doneFlowToSentCount}}" title="Enviado"
                        description="Qtd. de fluxo de mensagens enviados" color="text-green-500" icon="o-check"
                        tooltip="Quantidade de fluxos enviados" />
                </div>
                <div>
                    <x-stat value="{{($flowToSentCount - $doneFlowToSentCount)}}" title="Na fila"
                        description="Qtd. de fluxo de mensagens em espera" color="text-gray-500" icon="o-clock"
                        tooltip="Quantidade de fluxos em espera" />
                </div>
            </div>
            <br />
            <div class="flex justify-end">
                @if(!$sent->done && $sent->paused)
                <x-button spinner wire:click='playSent({{$sent->id}})' spinner icon="o-play-circle"
                    label="Continuar envio" class="btn-success" />
                @elseif(!$sent->done && !$sent->paused)
                <x-button spinner wire:click='pauseSent({{$sent->id}})' spinner icon="o-pause-circle"
                    label="Pausar envio" class="btn-warning" />
                @endif
            </div>
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