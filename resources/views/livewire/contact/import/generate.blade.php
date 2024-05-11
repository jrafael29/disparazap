<div class="mt-5">
    <h1 class="text-2xl mb-3">Gerador de leads</h1>
    {{-- The best athlete wants his opponent at his best. --}}
    <div>
        <x-form wire:submit="generate">
            <div class="flex gap-5">
                <div class="w-64">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selecione um estado</label>
                    <select wire:key="{{ $selectedUf }}" wire:model.live='selectedUf' id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @forelse ($ufs as $uf)
                            <option value="{{$uf['name']}}">{{$uf['name']}} -
                                @foreach($uf['ddds'] as $index => $ddd)
                                {{$ddd}}
                                @endforeach
                            </option>
                        @empty
                        <option> nenhum uf encontrado</option>
                        @endforelse
                    </select>
                </div>
                <div class="flex-1">
                    <x-range 
                            min="10"
                            max="500"
                            step="10" 
                            wire:model.live.debounce="count" 
                            label="Quantidade de números" 
                            hint="Arraste para selecionar a qtd. de números que serão gerados" />
                        {{$count}}
                </div>
            </div>
            <x-slot:actions>
                <x-button label="Cancel" />
                <x-button label="Gerar" @click="$wire.openModal = true" class="btn-primary" />
            </x-slot:actions>
        </x-form>

        <div>

        
        <x-modal wire:model="openModal" class="backdrop-blur">
            <div class="mb-5">Deseja mesmo gerar os <span class="font-extrabold">{{$count}}</span> números para o estado de <span class="font-extrabold">{{$selectedUf}}</span>?</div>
            <x-button label="Cancel" @click="$wire.openModal = false" />
            <x-button class="btn-primary" label="Gerar" wire:click='generate' spinner="generate" @click="$wire.openModal = false" />
        </x-modal>
        </div>
    </div>

</div>
