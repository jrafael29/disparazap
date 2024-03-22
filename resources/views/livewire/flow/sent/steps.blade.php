<div class="my-3">
    <x-steps wire:model="step" class="border my-5 p-5">
        <x-step step="1" text="Instancias">

            <div class="">
                <div class="mb-5">
                    <h1 class="text-2xl">Selecione quais instancias você deseja utilizar no disparo</h1>
                </div>

                <x-form action="">
                    <x-choices label="Instancias" wire:model="selectedInstances" :options="$instances" />
                </x-form>

            </div>


        </x-step>
        <x-step step="2" text="Alvos">
            <div>
                <div>
                    <div class="mb-5">
                        <h1 class="text-2xl">Seleciona para quem deseja enviar</h1>
                    </div>
                    @foreach($sendOptions as $key => $option)
                    @if($key === $sendOption)
                    <x-button spinner class="btn-outline" wire:key='{{$key}}' wire:click="selectSendOption('{{$key}}')">
                        {{$option}}
                    </x-button>

                    @else
                    <x-button spinner wire:key='{{$key}}' wire:click="selectSendOption('{{$key}}')">{{$option}}
                    </x-button>

                    @endif
                    @endforeach
                    <div class="">
                        @switch($sendOption)
                        @case('group-contacts')
                        <div>
                            <div class="mb-3">
                                <h1 class="text-2xl">Seus grupos...</h1>
                                <p>Selecione até {{$public_max_groups_selected_allowed}} grupos.</p>
                            </div>
                            <div class="max-h-80 font-mono overflow-auto">
                                @forelse($selectedInstancesGroups as $instanceId => $groups)
                                <div class="mb-3">
                                    @php
                                    $instance = \App\Models\Instance::query()->find($instanceId);
                                    @endphp
                                    <x-card title="Grupos da instancia: {{$instance->description}}"
                                        subtitle="{{count($groups)}} Grupos Encontrados.">
                                        <div class="flex gap-5 flex-wrap">
                                            @forelse($groups as $group)
                                            @php
                                            $id = $group['id'] . ":$instance->id";
                                            $index = $this->groupsSelected->search($id);
                                            @endphp
                                            <div class="border p-5 rounded {{$index === false ? '' : 'bg-gray-300'}}">
                                                <h1 class="text-center">{{$group['subject']}}</h1>
                                                <div class="flex justify-center mt-5">
                                                    <x-button wire:click="selectGroup('{{$id}}')">
                                                        @if($index === false)
                                                        Selecionar Grupo
                                                        @else
                                                        Esquecer Grupo
                                                        @endif
                                                    </x-button>
                                                </div>

                                            </div>
                                            @empty
                                            <h1>alguma coisa</h1>
                                            @endforelse
                                        </div>
                                    </x-card>
                                </div>
                                @empty
                                <h1 class="text-3xl">Nenhum grupo encontrado</h1>
                                @endforelse
                            </div>
                        </div>
                        @break
                        @case('raw-text')
                        <div class="my-3">
                            <div class="mb-3">
                                <h1 class="text-2xl">Informe um número abaixo do outro, sem pontuação</h1>
                            </div>

                            <x-form>
                                <x-textarea label="Numeros" wire:model="rawText" placeholder="Digite um numero abaixo do outro. ex:
                                    5581991827364
                                    5581991827366
                                    5581991827368" rows="5" inline />
                            </x-form>
                        </div>
                        @break
                        @case('import-excel')
                        <div>
                            <h1 class="text-2xl">Importe uma planilha</h1>
                        </div>
                        @break
                        @endswitch
                    </div>
                </div>
            </div>
        </x-step>
        <x-step step="3" text="Verificação" class="">
            <div>
                <div class="mb-3">
                    <h1> Revise os numeros a serem enviados.</h1>

                    <p>Foram encontrados {{$countAllPhonenumbers}}, sendo {{$countExistentPhonenumbers}} existentes, e
                        {{$countInexistentPhonenumbers}} inexistentes</p>
                </div>

                <div class="max-h-80 font-mono overflow-auto">
                    @switch($sendOption)
                    @case('group-contacts')
                    @forelse($groupsParticipantsPhonenumber as $groups)
                    @forelse($groups as $groupJid => $participants)

                    <x-card title="{{$groupJid}}">

                        <div class="flex flex-wrap gap-5">
                            @forelse($participants as $key => $phonenumber)
                            <div class="bg-green-800 p-5 rounded">
                                {{$phonenumber}}
                            </div>
                            @empty
                            <h1 class="text-1xl">Nenhum participante no grupo...</h1>
                            @endforelse

                        </div>


                    </x-card>
                    @empty
                    <h1 class="text-1xl">Nenhum participante no grupo...</h1>

                    @endforelse
                    @empty
                    <h1 class="text-2xl">Nada aqui...</h1>
                    @endforelse
                    @break
                    @case('raw-text')
                    <div class="flex flex-wrap gap-5">
                        @if($this->allowRepeatTarget == false)
                        @forelse($phonenumbers as $phonenumber => $exist)
                        @if(!empty($exist))
                        <div class="bg-green-500 p-5 rounded">
                            {{$phonenumber}}
                        </div>
                        @else
                        <div class="bg-red-800 p-5 rounded">
                            <p class="text-red-500 text-1xl">Numero inexistente</p>
                            <p class="line-through">{{$phonenumber}}</p>
                        </div>
                        @endif

                        @empty
                        <h1 class="text-1xl">Nenhum participante no grupo...</h1>
                        @endforelse
                        @else
                        @forelse($phonenumbers as $key => $phonenumber)

                        <div class="bg-green-800 p-5 rounded">
                            {{$phonenumber}}
                        </div>

                        @empty
                        <h1 class="text-1xl">Nenhum participante no grupo...</h1>
                        @endforelse
                        @endif
                    </div>
                    @break
                    @endswitch
                </div>
            </div>
        </x-step>
        <x-step step="4" text="Agendamento" class="">
            <div>
                <h1> Selecione uma data/horario para iniciar o envio.</h1>

                <div>

                    <div class="mb-5">
                        <x-datetime class="" required label="Data e horario do envio" wire:model="toSendDate"
                            icon="o-calendar" type="datetime-local" />
                    </div>

                    <div class="mb-5">
                        <x-range wire:model.live.debounce="delay"
                            label="Arraste para alterar o tempo entre as conversas"
                            hint="É o tempo entre um chat e outro, menor tempo maior risco de bloqueio no WhatsApp"
                            min="{{$minDelay}}" max="{{$maxDelay}}" />
                        <span class="text-2xl">
                            {{$delay}}
                            segundos
                        </span>
                    </div>

                    <div class="w-full flex justify-end">
                        <x-button spinner wire:click='handleFinalizeClick' class="btn-primary w-full" type="submit">
                            Agendar disparo
                        </x-button>
                    </div>

                </div>
            </div>
        </x-step>
        <x-step step="5" data-content="✓" step-classes="!step-success" text="Feito" class="">
            <div>
                <div class="mb-3">
                    <x-alert icon="o-exclamation-triangle" class="alert-success">
                        <strong> Disparo agendado com sucesso.</strong>
                    </x-alert>
                </div>
                <div>
                    <p class="mb-3">
                        <span class="text-2xl">Estimativa da duração do disparo:</span>
                        <span class="text-2xl">
                            @if(count($selectedInstances) > 0 && count($phonenumbers) > 0)

                            @if ($hours > 0)
                            {{ $hours }} horas,
                            @endif

                            @if ($minutes > 0)
                            {{ $minutes }} minutos e
                            @endif

                            @if ($seconds > 0)
                            {{ $seconds }} segundos
                            @endif
                            @endif
                        </span>
                    </p>
                    <p class="mb-3">
                        <span class="text-2xl">O disparo inicia em:</span>
                        <span class="text-2xl">
                            @if($toSendDate)

                            @php
                            $dateDiff=\Carbon\Carbon::now()->diff(\Carbon\Carbon::parse($toSendDate))
                            @endphp

                            @if($dateDiff->days)
                            {{$dateDiff->days}} {{$dateDiff->days != 0 && $dateDiff->days == 1 ? 'dia' : 'dias'}},
                            @endif

                            @if($dateDiff->h)
                            {{$dateDiff->h}} {{$dateDiff->h != 0 && $dateDiff->h == 1 ? 'hora' : 'horas'}} e
                            @endif

                            @if($dateDiff->i)
                            {{$dateDiff->i}} {{$dateDiff->i != 0 && $dateDiff->i == 1 ? 'minuto' : 'minutos'}}
                            @endif

                            @endif
                        </span>
                    </p>
                </div>
            </div>
        </x-step>
    </x-steps>

    @if($step !== $steps)
    @if($step === 1)
    @else
    <x-button class="btn-outline" label="Voltar" wire:click="prev" />
    @endif
    @if($step === $steps - 1)
    @else
    <x-button spinner class="btn-primary" label="Avançar" wire:click="next" />
    @endif
    @endif
</div>