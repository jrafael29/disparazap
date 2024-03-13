<div class="my-3">
    {{-- The best athlete wants his opponent at his best. --}}

    <div class="flex flex-wrap gap-5 justify-center">
        @forelse($flowToSents as $flowToSent)

        <x-card title="{{$flowToSent->flow->description}}" class="">
            <x-slot:menu>
                <x-button icon="o-trash" type="button" wire:click='handleDeleteFlowToSentClick({{$flowToSent->id}})'
                    class="ml-4 btn-outline text-red-500 btn-sm" />
            </x-slot:menu>

            <p>Destinario: +{{ $flowToSent->to}}</p>

            @if($flowToSent->busy === 0 && $flowToSent->sent === 0)
            <p>Será enviado em: <span class="text-blue-300">
                    {{\Carbon\Carbon::parse($flowToSent->to_sent_at)->format('H:i d/m/Y')}}<span> </p>
            @elseif($flowToSent->busy === 1 && $flowToSent->sent === 1)
            <p>Enviado as: <span class="text-green-500"> {{\Carbon\Carbon::parse($flowToSent->updated_at)->format('H:i
                    d/m/Y')}}</span></p>

            @elseif($flowToSent->busy === 1 && $flowToSent->sent === 0)
            <p class="text-yellow-500">Em envio...</p>
            @endif
        </x-card>
        @empty
        <div>
            <div class="text-3xl">Nenhum envio encontrado.</div>
        </div>
        @endforelse
    </div>
</div>