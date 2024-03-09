<div>
    <x-steps wire:model="step" class="border my-5 p-5">
        <x-step step="1" text="Instancias">

            <div class="">
                <h1>Selecione quais instancias você deseja utilizar</h1>

                <x-form action="">
                    <x-choices label="Instancias" wire:model="instances_multi_ids" :options="$instances" />
                </x-form>

            </div>


        </x-step>
        <x-step step="2" text="Alvos">
            <div>

                <div>

                    <div>
                        <h1>Seleciona para quem deseja enviar</h1>
                        @foreach($sendOptions as $key => $option)
                        @if($key === $sendOption)
                        <x-button class="btn-outline" wire:key='{{$key}}' wire:click="selectSendOption('{{$key}}')">
                            {{$option}}
                        </x-button>

                        @else
                        <x-button wire:key='{{$key}}' wire:click="selectSendOption('{{$key}}')">{{$option}}</x-button>

                        @endif
                        @endforeach
                        <div class="">


                            @switch($sendOption)

                            @case('group-contacts')
                            <div>
                                <h1 class="text-2xl">Seus grupos...</h1>

                                <div>
                                    @forelse($instances_groups as $instanceId => $groups)
                                    @php
                                    $instance = \App\Models\Instance::query()->find($instanceId);
                                    @endphp
                                    <x-card title="Grupos da instancia: {{$instance->description}}"
                                        subtitle="{{count($groups)}} Grupos Encontrados.">
                                        <div class="flex gap-5 flex-wrap">
                                            @forelse($groups as $group)
                                            @php
                                            $index = $this->groupsSelected->search($group['id']);
                                            @endphp
                                            <div
                                                class="p-5 rounded {{$index === false ? 'bg-blue-900' : 'bg-red-900'}}">
                                                <h1>{{$group['subject']}}</h1>
                                                <x-button wire:click="selectGroup('{{$group['id']}}')">
                                                    @if($index === false)
                                                    Selecionar Grupo
                                                    @else
                                                    Esquecer Grupo
                                                    @endif
                                                </x-button>
                                            </div>
                                            @empty
                                            <h1>alguma coisa</h1>
                                            @endforelse
                                        </div>
                                    </x-card>
                                    @empty
                                    <h1 class="text-3xl">Nenhum grupo encontrado</h1>
                                    @endforelse
                                </div>
                            </div>
                            @break
                            @case('raw-text')
                            <div>
                                <h1 class="text-2xl">Informe um texto abaixo do outro, sem pontuação</h1>

                                <x-form action="">
                                    <x-textarea label="Numeros" wire:model="bio" placeholder="Digite um numero abaixo do outro. ex:
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
                            Opção selecionada {{$sendOption}}



                        </div>



                    </div>

                </div>

            </div>
        </x-step>
        <x-step step="3" text="Agendamento" class="">
            <div>
                <h1> Selecione uma data/horario para iniciar o envio. </h1>
            </div>
        </x-step>
    </x-steps>

    <x-button class="btn-outline" label="Previous" wire:click="prev" />
    <x-button class="btn-primary" label="Next" wire:click="next" />
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
</div>