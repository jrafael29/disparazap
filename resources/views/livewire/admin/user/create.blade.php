<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div>
        <x-modal wire:model="openModal" title="Novo usuario" subtitle="Cadastre um novo usuário" separator>
            <x-form wire:submit="save">
                <div>
                    {{-- Custom CSS class. Remeber to configure Tailwind safelist --}}
                    <x-input label="Nome" wire:model.lazy="name" placeholder="Nome do usuário" clearable />
                    <x-input label="Email" type="email" placeholder="Email do usuário" hint="" wire:model.lazy="email" error-class="text-red-500 p-1" clearable />
                    <x-input label="Password" placeholder="Senha do usuário" wire:model.lazy="password" icon="o-eye" type="password" clearable />
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