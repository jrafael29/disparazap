<div>
    <div class="mb-3">
        <a wire:navigate href="{{route('contact')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>
    <!-- HEADER -->
    <x-header title="Gerenciar grupos de contatos" subtitle="Adicione contatos a seus grupos" separator
        progress-indicator>
        <x-slot:actions>

            <div>
                <livewire:contact.group.form-modal />
            </div>

        </x-slot:actions>
    </x-header>

    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}

    <div>
        <div class="h-96 overflow-y-auto">
            <x-table :headers="$headers" :rows="$groups" wire:model="expanded" expandable>


                @scope('cell_identifier', $group)
                #
                @endscope

                @scope('cell_contactsCount', $group)
                {{$group->userContacts->count()}}
                @endscope

                @scope('expansion', $group)
                <div class="bg-base-200 dark:bg-zinc-800 p-8 ">


                    <div class="flex flex-col sm:flex-row justify-between items-center my-2 ">
                        <h1 class="text-2xl">Contatos do grupo: {{$group->name}}</h1>
                        <div class="flex gap-2">
                            <x-button icon="o-trash" tooltip-top="Excluir Grupo"
                                wire:click='deleteUserGroup({{$group->id}})' class="btn-error" />
                            <a wire:navigate href="{{route('contact.import')}}">
                                <x-button type="submit" label="Importar contatos" class="btn-outline" />
                            </a>

                        </div>

                    </div>
                    <br />
                    <div class=" font-mono overflow-auto bg-base-300 dark:bg-zinc-900 p-3">
                        <div class="flex flex-col flex-wrap gap-2">

                            @forelse($group->userContacts->take(50) as $contacts)
                            <span class="w-1/2">
                                {{$contacts->contact->phonenumber}}
                            </span>
                            @empty
                            <p class="text-2xl">Nenhum contato neste grupo.</p>
                            @endforelse

                        </div>
                    </div>


                </div>
                @endscope
            </x-table>

        </div>
    </div>
</div>