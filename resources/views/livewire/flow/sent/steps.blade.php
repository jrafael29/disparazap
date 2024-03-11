<div>
    <x-steps wire:model="step" class="border my-5 p-5">
        <x-step step="1" text="Instancias">

            <div class="">
                <div class="mb-5">
                    <h1 class="text-2xl">Selecione quais instancias você deseja utilizar no disparo</h1>
                </div>

                <x-form action="">
                    <x-choices label="Instancias" wire:model="instances_multi_ids" :options="$instances" />
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
                                @forelse($instances_groups as $instanceId => $groups)
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
                                            <div
                                                class="p-5 rounded {{$index === false ? 'bg-gray-800' : 'bg-blue-900'}}">
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
                        @forelse($phonenumbers as $key => $phonenumber)
                        <div class="bg-green-800 p-5 rounded">
                            {{$phonenumber}}
                        </div>
                        @empty
                        <h1 class="text-1xl">Nenhum participante no grupo...</h1>
                        @endforelse
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
                        <x-datetime class="text-white" required label="Data e horario do envio" wire:model="toSendDate"
                            icon="o-calendar" type="datetime-local" />
                    </div>

                    <div class="mb-5">
                        <x-range wire:model.live.debounce="delay"
                            label="Arraste para alterar o tempo entre as conversas"
                            hint="É o tempo entre um chat e outro, menor tempo maior risco de bloqueio no whatsapp"
                            min="15" max="35" />
                        <span class="text-2xl">
                            {{$delay}}
                            segundos
                        </span>
                    </div>

                    <div>
                        <x-button spinner wire:click='handleFinalizeClick' class="btn-primary" type="submit">Agendar
                        </x-button>
                    </div>

                </div>
            </div>
        </x-step>
        <x-step step="5" text="Detalhes" class="">
            <div>
                <h1> Selecione uma data/horario para iniciar o envio.</h1>

                <div>
                    <h1 class="text-3xl">Estimativa da duração do disparo:</h1>
                    <span class="text-3xl">
                        @if(count($instances_multi_ids) > 0 && count($phonenumbers) > 0)

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
                </div>
            </div>
        </x-step>
    </x-steps>

    @if($step !== 5)
    @if($step === 1)
    @else
    <x-button class="btn-outline" label="Voltar" wire:click="prev" />
    @endif
    @if($step === $steps)
    @else
    <x-button spinner class="btn-primary" label="Avançar" wire:click="next" />
    @endif
    @endif
</div>