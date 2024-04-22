<div>
    <h1 class="text-3xl my-4">Extrair contatos de texto</h1>
    <x-steps wire:model="step" class="border my-5 p-5">
        <x-step step="1" text="Inserir texto">
            <div class="my-4">
                <x-collapse wire:model="show" separator class="bg-base-200">
                    <x-slot:heading wire:click='toggleCollapseForm'>Configurações avançadas
                    </x-slot:heading>
                    <x-slot:content>

                        <div>
                            <div class="my-2">
                                <x-button class="btn-outline" label="{{$includeDdd ? ' Não Incluir' : 'Incluir' }} DDD"
                                    tooltip-right="Incluir DDD a número que não tiver" wire:click='toggleIncludeDdd' />

                            </div>
                            @if($includeDdd)

                            <x-input wire:model.live='ddd' type="number" label="Insira o DDD a ser incluido nos numeros"
                                inline />
                            @endif
                        </div>
                        <div>
                            <div class="my-2">
                                <x-button class="btn-outline" label="{{$includeDdi ? ' Não Incluir' : 'Incluir' }} DDD"
                                    tooltip-right="Incluir DDI a número que não tiver" label="Incluir DDI Do País"
                                    wire:click='toggleIncludeDdi' />
                            </div>
                            @if($includeDdi)
                            <x-input wire:model.live='ddi' type="number" label="Insira o DDI a ser incluido nos numeros"
                                inline />
                            @endif
                        </div>

                    </x-slot:content>
                </x-collapse>

            </div>

            <x-form wire:submit='handleSubmit'>
                <x-textarea label="Texto com números"
                    hint="Cole qualquer texto com os números de telefone que iremos extrair para você."
                    wire:model="rawText"
                    placeholder="Cole qualquer texto que será extraido apenas os números de telefone" rows="5"
                    inline />

                <x-slot:actions>
                    <x-button label="Limpar" type="reset" />

                    <x-button label="Extrair" class="btn-primary" type="submit" spinner="handleSubmit" />
                </x-slot:actions>
            </x-form>
        </x-step>
        <x-step step="2" text="Revisão">
            <div>
                @if(count($phonenumbers))
                <div>
                    <div class="mb-3">
                        <h1 class="text-2xl"> Revise os numeros encontrados no texto.</h1>
                    </div>
                    <div class="max-h-80 font-mono overflow-auto">

                        <div class="flex">
                            <div class="w-1/2 ">
                                @forelse($phonenumbers as $phonenumber)
                                {{$phonenumber}} <br />
                                @empty
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
                <div>
                    <h1 class="text-2xl">Esses foram os números encontrados</h1>

                    @if($userOnlineInstancesCount > 0)
                    <p class="text-1xl">Verificar existencia dos numeros no whatsapp?</p>

                    <div class="gap-2 flex justify-end">
                        <x-button label="Voltar" class="btn-outline" wire:click='prev' spinner />
                        <x-button label="Verificar existencia" class="btn-primary" wire:click='checkExistence'
                            spinner />
                    </div>


                    <br />


                    @else
                    <div class="w-full">
                        <x-alert title="Conecte uma instancia ao WhatsApp para salvar contatos"
                            icon="o-exclamation-triangle" class="alert-warning" shadow>
                            <x-slot:actions>
                                <a wire:navigate href="{{route('instance')}}">
                                    <x-button label="Conectar uma instancia" />
                                </a>
                            </x-slot:actions>
                        </x-alert>
                    </div>
                    @endif

                </div>
                @endif
            </div>

        </x-step>
        <x-step step="3" text="Salvar contatos" class="">
            <div>
                <div class="">
                    <h1 class="text-2xl mb-2">Deseja salvar os contatos válidos?</h1>
                    <x-button label="Confirmar Salvar Contatos" class="btn-info" wire:click='saveExistentPhonenumbers' spinner />
                </div>
            </div>
        </x-step>
        {{-- <x-step step="4" text="Adicionar a grupo">
            <div>
                Deseja adicionar os contatos a algum grupo?

                <br />

                <div>
                    <livewire:contact.group.form-modal />
                </div>


                Criar grupo
                <br />

                <h1>Seus grupos:</h1>

                <div class="flex">
                    @forelse($groups as $group)
                    <div class="border p-5 rounded {{$group->id !== $groupSelectedId ? '' : 'bg-gray-300'}}">
                        <h1 class="text-center">{{$group->name}}</h1>
                        <p>Esse grupo já possui {{count($group->userContacts) > 0 ? $group->userContacts->count() : 0}}
                            contatos</p>
                        <div class="flex justify-center mt-5">
                            <x-button wire:click="selectGroup({{$group->id}})">
                                @if($group->id === $groupSelectedId)
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

                @if($groupSelectedId)
                <x-button class="btn-primary" wire:click='addContactsToGroup' spinner
                    label="Adicionar contatos a um grupo existente" />
                @endif
            </div>
        </x-step> --}}

    </x-steps>

    {{-- Create some methods to increase/decrease the model to match the step number --}}
    {{-- You could use Alpine with `$wire` here --}}
    {{--
    <x-button label="Previous" wire:click="prev" />
    <x-button class="btn-info" label="Next" wire:click="next" /> --}}






    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
</div>