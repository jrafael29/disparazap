<div>
    <div class="mb-3">
        <a wire:navigate href="{{route('contact')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>
    <x-header title="Novo contato" subtitle="Crie um novo contato personalizado." separator progress-indicator>
    </x-header>

    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-form wire:submit="save">
        {{-- Custom CSS class. Remeber to configure Tailwind safelist --}}
        <x-input label="Descrição" hint="" wire:model="description" error-class="bg-blue-500 p-1" />
        <x-input label="Número" hint="Telefone" wire:model="phonenumber" error-class="bg-blue-500 p-1" />
        <x-slot:actions>
            <x-button label="Limpar" type="reset" />
            <x-button label="Cadastrar" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>