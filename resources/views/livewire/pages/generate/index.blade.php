<div>
    <x-header title="Gerar números de telefone"
        subtitle="Aqui você pode gerar números de telefones com DDD personalizado"
        separator progress-indicator>
    </x-header>
    {{-- The whole world belongs to you. --}}

    <div class="mb-4">
        <x-form wire:submit="save">
            {{-- Notice `error_field` --}}
            <div class="flex flex-col sm:flex-row gap-5 justify-center">
                <x-input type="number" label="DDI" hint="DDI personalizado para números gerados" wire:model="ddi" error-field="total_salary" disabled />
                <x-input type="number" label="DDD" hint="DDD personalizado para números gerados" wire:model="ddd" error-field="total_salary" />
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
                <x-button wire:click='cancel' label="Limpar" />
                <x-button label="Gerar números" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>   
    </div>
    
    @if(count($generatedPhonenumbers))
    <div class="mb-3">
        <x-button wire:click='copy' class="btn btn-outline" onclick="copyToClipboard.run({{json_encode($generatedPhonenumbers)}})">Copiar números</x-button>
    </div>
    @endif
    <div class="h-80 overflow-y-auto">
        <div id="generated-phonenumbers-area" class="flex flex-wrap justify-evenly gap-1">
            @forelse ($generatedPhonenumbers as $phonenumber)
            <div class="p-1 ">
                {{$phonenumber}}
            </div>
            @empty
                
            @endforelse
        </div>
    </div>
    

        <script>
            window.copyToClipboard = {
                run(numbers) { 
                    navigator.clipboard.writeText(numbers.join('\n'));
                }
            };
        </script>

</div>
