<div class="">
    {{-- The whole world belongs to you. --}}
    {{-- @if(count(Auth::user()->instances) === 0) --}}
    <x-form wire:submit="handleSubmit">
        <x-input label="Descrição" type="description" wire:model="description" placeholder="Minha instancia vivo" />
        <x-input label="Número" maxlength="13" hint="Número do telefone que se conectará" wire:model="phonenumber"
            placeholder="558191090132" />

        <x-slot:actions>
            <x-button label="Limpar Campos" type="reset" />
            <x-button label="Salvar Instancia" class="btn-primary" type="submit" spinner="handleSubmit" />
        </x-slot:actions>
    </x-form>
    {{-- @endif --}}
</div>