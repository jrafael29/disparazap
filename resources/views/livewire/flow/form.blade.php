<div class="">
    {{-- The whole world belongs to you. --}}
    <x-form wire:submit="handleSubmit">
        <x-input label="Descrição" type="description" wire:model="description" placeholder="Fluxo de boas vindas" />
        <x-slot:actions>
            <x-button label="Limpar Campos" type="reset" />
            <x-button label="Salvar Fluxo" class="btn-primary" type="submit" spinner="handleSubmit" />
        </x-slot:actions>
    </x-form>
</div>