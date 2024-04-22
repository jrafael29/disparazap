<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    <div class="mb-3">
        @if($this->showGroups)
        <div class="mb-3">
            <h1>Seus grupos:</h1>
            <div class="flex mb-3">
                @forelse($this->groups as $group)
                <div wire:key='{{$group->id}}'>
                    <div class="border p-5 rounded {{$group->id !== $this->groupSelectedId ? '' : 'bg-gray-300'}}">
                        <h1 class="text-center">{{$group->name}}</h1>
                        <p>Esse grupo possui {{count($group->userContacts) > 0 ? $group->userContacts->count() : 0}}
                            contatos</p>
                        <div class="flex justify-center mt-5">
                            @if($group->id === $this->groupSelectedId)
                                    <x-button spinner wire:click="selectGroup({{$group->id}})">
                                        Grupo Selecionado
                                    </x-button>
                                @else
                                    <x-button class="btn-outline" spinner wire:click="selectGroup({{$group->id}})">
                                        Selecionar Grupo
                                    </x-button>
                                @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-full">
                    <x-alert title="Ops... Nenhum grupo encontrado...." icon="o-exclamation-triangle" class="alert-warning">
                        <x-slot:actions>
                            <a href="{{route('groups')}}" wire:navigate>
                                <x-button label="Cadastre um novo grupo" />
                            </a>
                        </x-slot:actions>
                    </x-alert>
                </div>
                @endforelse
            </div>

            @if($this->groupSelectedId)
            <x-button class="btn-primary" wire:click='addVerifiedPhonenumberCheckToGroup' spinner
                label="Adicionar contatos a um grupo existente" />
            @endif
        </div>
        @endif
    </div>

</div>
