<div class="">
    {{-- The whole world belongs to you. --}}
    {{-- @if(count(Auth::user()->instances) === 0) --}}
    <x-form wire:submit="handleSubmit">

        <x-textarea max=500 label="Texto" wire:model="text" placeholder="Digite o texto" hint="O texto da mensagem"
            rows="3" inline />
        <x-toggle label="Adicionar 55 ao numeros que não tiver" hint="55 é o prefixo DDI do Brasil."
            wire:model="includeDdi" />
        <x-slot:actions>
            <x-button label="Limpar Campos" type="reset" />
            <x-button label="Extrair" class="btn-primary" type="submit" spinner="handleSubmit" />
        </x-slot:actions>
    </x-form>

    <div>

        @forelse($encouteredPhonenumbers as $phonenumber)
        <div>
            {{$phonenumber}}
        </div>
        @empty
        <h1>nenhum numero encontrado</h1>
        @endforelse
    </div>

    {{-- @endif --}}
</div>