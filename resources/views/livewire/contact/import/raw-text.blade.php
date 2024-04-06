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
                                    wire:click='toggleIncludeDdd' />
                            </div>
                            @if($includeDdd)
                            <x-input wire:model.live='ddd' type="number" label="Insira o DDD a ser incluido nos numeros"
                                inline />
                            @endif
                        </div>
                        <div>
                            <div class="my-2">
                                <x-button class="btn-outline" label="{{$includeDdi ? ' Não Incluir' : 'Incluir' }} DDD"
                                    label="Incluir DDI Do País" wire:click='toggleIncludeDdi' />
                            </div>
                            @if($includeDdi)
                            <x-input wire:model.live='ddi' type="number" label="Insira o DDD a ser incluido nos numeros"
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
                    placeholder="Digite qualquer texto que será extraido apenas os números de telefone" rows="5"
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
                @if(!empty($existentPhonenumbers) || !empty($inexistentPhonenumbers))

                <div class="">
                    <p>Foram encontrados {{count($existentPhonenumbers)}} telefones (whatsapp) existentes, e
                        {{count($inexistentPhonenumbers)}} inexistentes</p>
                </div>
                <div class="flex my-4">
                    <div class="w-1/2 ">
                        <p class="text-2xl">Numeros existentes</p>
                        @forelse($existentPhonenumbers as $phonenumber)
                        {{$phonenumber}} <br />
                        @empty
                        <p class="text-1xl">Nenhum contato existente</p>
                        @endforelse
                    </div>
                    <div class="w-1/2 bg-red-200">
                        <p class="text-2xl">Numeros inexistentes</p>
                        @forelse($inexistentPhonenumbers as $phonenumber)
                        {{$phonenumber}} <br />
                        @empty
                        <p class="text-1xl">Nenhum contato inexistente</p>
                        @endforelse
                    </div>
                </div>
                <div class="">
                    <h1 class="text-2xl mb-2">Deseja salvar os contatos válidos?</h1>
                    <x-button label="Salvar Contatos" class="btn-info" wire:click='saveExistentPhonenumbers' spinner />
                </div>
                @endif
            </div>
        </x-step>
    </x-steps>

    {{-- Create some methods to increase/decrease the model to match the step number --}}
    {{-- You could use Alpine with `$wire` here --}}
    {{--
    <x-button label="Previous" wire:click="prev" />
    <x-button class="btn-info" label="Next" wire:click="next" /> --}}






    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
</div>