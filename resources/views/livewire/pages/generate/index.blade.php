<div>
    <x-header title="Gerar números de telefone"
        subtitle="Aqui você pode gerar números de telefones"
        separator progress-indicator>
    </x-header>
    {{-- The whole world belongs to you. --}}

    <div class="mb-4">
        <x-form wire:submit="save">
            {{-- Notice `error_field` --}}
            <div class="flex flex-col sm:flex-row gap-5 justify-center">
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
            </div>
            <x-slot:actions>
                <x-button type="reset" label="Cancelar" />
                <x-button label="Gerar números" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>   
    </div>
    

    <div class="flex flex-wrap">
        @forelse ($generatedPhonenumbers as $phonenumber)
        <div class="p-1">
            {{$phonenumber}}
        </div>
        @empty
            
        @endforelse
    </div>

</div>
