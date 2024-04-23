<div>

    <x-steps wire:model="step" class="border my-5 p-5">
        <x-step step="1" text="Selecionar grupos">
            <div>
                <div class="mb-3">
                    <h1 class="text-2xl">Seus grupos...</h1>
                    <p>Selecione até {{$public_max_groups_selected_allowed}} grupos.</p>
                </div>
                <div class="max-h-80 font-mono overflow-auto">
                    @forelse($instancesGroups as $instanceId => $instanceGroups)
                    <div class="mb-3">
                        @php   
                        $instance = \App\Models\Instance::query()->find($instanceId)
                        @endphp
                        <x-card title="Grupos da instancia: {{$instance->description}}"
                            subtitle="{{count($instanceGroups)}} Grupos Encontrados.">
                            <div class="flex gap-5 flex-wrap">
                                @forelse($instanceGroups as $group)
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
                                <h1 class="text-3xl">Nenhum grupo encontrado</h1>
                                @endforelse
                            </div>
                        </x-card>
                    </div>
                    @empty
                    <h1>Nenhuma instancia encontrada</h1>
                    @endforelse
                </div>
            </div>
        </x-step>
        <x-step step="2" text="Revisão">
            <div class="flex flex-wrap">
                @forelse($phonenumbers as $phonenumber)
                    <div class="m-2">
                        {{$phonenumber}}
                    </div>
                @empty
                    <h1>Nenhum telefone encontrado.</h1>
                @endforelse
            </div>
        </x-step>
        <x-step step="3" text="Direcionar" class="">
            <div>
                <div class="">
                    <h1 class="text-2xl mb-2">Adicione contatos a um Grupo</h1>
                    <div>
                        <div>
                            <div>
                                <livewire:contact.group.form-modal />
                            </div>
                            Criar grupo
                            <br />

                            <div class="flex">
                                @forelse($disparaGroups as $group)
                                <div class="border p-5 rounded {{$group->id !== $disparaGroupSelectedId ? '' : 'bg-gray-300'}}">
                                    <h1 class="text-center">{{$group->name}}</h1>
                                    <p>Esse grupo já possui {{count($group->userContacts) > 0 ? $group->userContacts->count() : 0}}
                                        contatos</p>
                                    <div class="flex justify-center mt-5">
                                        <x-button wire:click="selectDisparaGroup({{$group->id}})">
                                            @if($group->id === $disparaGroupSelectedId)
                                            Grupo Selecionado
                                            @else
                                            Selecionar Grupo
                                            @endif
                                        </x-button>
                                    </div>
                                </div>
                                @empty
                                <h1>alguma coisa</h1>
                                @endforelse
                            </div>
                        </div>

                    </div>
                    @if($disparaGroupSelectedId)
                    <div class="mt-4">
                        <x-button class="btn-primary" wire:click="handleSubmit">
                            Confirmar e salvar contatos
                        </x-button>
                    </div>
                    @endif
                </div>

            </div>
        </x-step>
            
    </x-steps>

    <div>
        @if($step < 3)
        <x-button spinner label="Voltar" wire:click="prev" />
        <x-button spinner class="btn-info" label="Avançar" wire:click="next" />
        @endif
    </div>
    
</div>
