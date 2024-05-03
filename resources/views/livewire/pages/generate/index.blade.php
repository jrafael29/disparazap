<div>
    <x-header title="Gerar números de telefone"
        subtitle="Aqui você pode gerar números de telefones"
        separator progress-indicator>
    </x-header>
    {{-- The whole world belongs to you. --}}
    <div class="mb-3">
        <h1 class="text-2xl">Gerar numeros de telefone</h1>
    </div>



    <x-form wire:submit="save">
        {{-- Notice `error_field` --}}
        
        <x-input label="DDI" hint="DDD Personalizado para os números gerados" wire:model="ddi" error-field="total_salary" disabled />
        <x-input label="DDD" hint="DDD Personalizado para os números gerados" wire:model="ddd" error-field="total_salary" />

        <div>
            <x-range 
                min="10"
                max="500"
                step="10" 
                wire:model.live.debounce="count" 
                label="Quantidade de números" 
                hint="Selecione quantos números quer gerar" />
            {{$count}}
        </div>

        <x-slot:actions>
            <x-button label="Cancel" />
            <x-button label="Click me!" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>

    <div class="flex flex-wrap">
        @forelse ($generatedPhonenumbers as $phonenumber)
        <div class="p-2">
            {{$phonenumber}}
        </div>
        @empty
            
        @endforelse
    </div>

</div>
