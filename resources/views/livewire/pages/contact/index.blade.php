<div>
    <!-- HEADER -->
    <x-header title="Gerenciar contatos" subtitle="Crie, Gerencie, Importe seus contatos." separator progress-indicator>
        <x-slot:actions>
            <a wire:navigate href="{{route('contact.groups')}}">
                <x-button tooltip-left="Grupo de contatos" icon="o-user-group" spinner class="btn-outline" />
            </a>
            <a wire:navigate href="{{route('contact.import')}}">
                <x-button tooltip-left="Importar contatos" icon="o-arrow-down-tray" spinner class="btn-outline" />
            </a>

            <x-button tooltip-left="Novo contato" icon="o-user-plus" spinner class="btn-outline"
                @click="$wire.openModal = true" />
            {{-- <a wire:navigate href="{{route('contact.create')}}">
                <x-button tooltip-left="Novo contato" icon="o-user-plus" spinner class="btn-outline" />
            </a> --}}


            <div>
                <x-modal wire:model="openModal" title="Novo contato" subtitle="Cadastre um novo contatos" separator>
                    <x-form wire:submit="handleSubmit">
                        <div>
                            {{-- Custom CSS class. Remeber to configure Tailwind safelist --}}
                            <x-input label="Descrição para o contato" hint="" wire:model.lazy="description"
                                error-class="bg-blue-500 p-1" />

                            <x-input label="Número do contato" hint="" wire:model.lazy="phonenumber"
                                error-class="bg-blue-500 p-1" />
                        </div>

                        <x-slot:actions>
                            <x-button label="Cancelar" @click="$wire.openModal = false" />
                            @if($isValidPhonenumber)
                            <x-button type="submit" label="Confirmar" class="btn-primary" />
                            @else
                            <x-button wire:click='validatePhonenumber' label="Validar número" class="btn-outline" />
                            @endif
                        </x-slot:actions>
                    </x-form>
                </x-modal>

            </div>

        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <div class="">
        <div class="mb-3">
            <x-select wire:change='orderContacts' label="Filtrar por DDD" icon="o-globe-americas"
                hint="Digite o numero para buscar mais rapido" :options="$ddds" wire:model="dddSelected" />
        </div>
        <div class="h-96 overflow-y-auto">
            <x-table selectable wire:model='selectedContacts' :headers="$headers" :rows="$contacts" striped
                @row-selection="$wire.updateSelectedContacts" @row-selection="console.log('eae')">


                @scope('actions', $contact)
                <x-button spinner icon="o-trash" wire:click="delete({{ $contact->id }})" spinner class="btn-sm" />
                @endscope
            </x-table>

        </div>
        <div>

            <x-button label="Excluir selecionados" icon="o-trash" wire:click="deleteSelectedContacts" spinner />
            <x-button label="Enviar para selecionados" icon="o-check" spinner />

        </div>
    </div>
</div>