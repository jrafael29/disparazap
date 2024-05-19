<div>
    {{-- In work, do what you enjoy. --}}
    <div class="bg-base-200 dark:bg-zinc-800 p-8 ">


        <div class="flex flex-col sm:flex-row justify-between items-center my-2 ">
            <h1 class="text-2xl">Contatos do grupo: {{$group->name}}</h1>
            <div class="flex gap-2">
                <div>
                    <x-button @click="$wire.modalDelete = true" type="submit" icon="o-trash" tooltip-bottom="Excluir grupo" class="btn-error" />
                </div>
                <div>
                    <x-button @click="$wire.modalExport = true" type="submit" icon="o-arrow-down-tray" tooltip-bottom="Baixar contatos" class="btn-outline" />
                </div>

                <a wire:navigate href="{{route('import')}}">
                    <x-button type="submit" icon="o-arrow-up-tray" tooltip-bottom="Importar contatos" class="btn-outline" />
                </a>
                <div>
                    <x-button @click="$wire.modalEdit = true" icon="o-pencil" tooltip-bottom="Editar grupo" class="btn-outline" />
                </div>

            </div>

            {{-- modals --}}

            {{-- edit modal --}}
            <x-modal wire:model="modalEdit" title="Editar grupo" separator>
                <x-form wire:submit="handleEditSubmit">
                    <x-input label="Nome" hint="Número do telefone que se conectará" wire:model="name" />
                    <x-input label="Descrição" id="description" type="description" wire:model="description"/>
                    <x-slot:actions>
                        <x-button label="Cancelar" @click="$wire.modalEdit = false" />
                        <x-button label="Editar" type="submit" class="btn-primary" />
                    </x-slot:actions>
                </x-form>
            </x-modal>

            {{-- end edit modal --}}

            {{-- modal delete --}}

            <x-modal wire:model="modalDelete" class="backdrop-blur" title="Confirmar exclusão">
                <div class="mb-5">Confirme para excluir o grupo `<span class="font-extrabold">{{$group->name}}</span>`.</div>
                <div class="flex justify-center gap-5 ">
                    <x-button label="Cancelar" @click="$wire.modalDelete = false" />
                    <x-button label="Confirmar Exclusão" wire:click='deleteUserGroup' class="btn-error" />
                </div>
            </x-modal>
            {{-- end modal delete --}}

            {{-- export modal --}}

            <x-modal wire:model="modalExport" class="backdrop-blur" title="Exportar contatos" >
                <div class="mb-5">Confirme para Baixar/Exportar os contatos do grupo `<span class="font-extrabold">{{$group->name}}</span>`.</div>
                <div class="flex justify-center gap-5 ">
                    <x-button label="Cancelar" @click="$wire.modalExport = false" />
                    <x-button label="Baixar contatos" wire:click='export' class="btn-primary" spinner="export" />
                </div>
            </x-modal>

            {{-- end export modal --}}

            {{-- end modals --}}

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
</div>
