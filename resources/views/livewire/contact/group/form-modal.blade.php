<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div>
        <x-modal wire:model="openModal" title="Novo grupo" subtitle="Cadastre um novo grupo de contatos" separator>
            <x-form wire:submit="save">
                <div>
                    {{-- Custom CSS class. Remeber to configure Tailwind safelist --}}
                    <x-input label="Nome do grupo" hint="" wire:model.lazy="name" error-class="bg-blue-500 p-1" />

                    <x-input label="Descrição" hint="" wire:model.lazy="description" error-class="bg-blue-500 p-1" />
                </div>

                <x-slot:actions>
                    <x-button label="Limpar" type="reset" />
                    <x-button type="submit" label="Confirmar" class="btn-primary" />
                </x-slot:actions>
            </x-form>
        </x-modal>

    </div>

    <x-button tooltip-left="Novo grupo" icon="o-plus" spinner class="btn-outline" @click="$wire.openModal = true" />

</div>