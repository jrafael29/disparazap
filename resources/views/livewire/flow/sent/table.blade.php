<div class="my-3">
    {{-- The best athlete wants his opponent at his best. --}}
    <div class="mb-4">
        @if(count($flowToSents))
        <div>
            <x-alert title="Abaixo está todos os envios" description="Envios agendados" icon="o-envelope" shadow>
            </x-alert>
        </div>

        @endif
    </div>
    <div class="flex flex-wrap gap-5 justify-center lg:justify-start">
        @forelse($flowToSents as $flowToSent)

        <x-card title="{{$flowToSent->flow->description}}" class="">
            <x-slot:menu>
                <x-button icon="o-trash" type="button" wire:click='handleDeleteFlowToSentClick({{$flowToSent->id}})'
                    class="ml-4 btn-outline text-red-500 btn-sm" />
            </x-slot:menu>

            <p>Destinario: +{{ $flowToSent->to}}</p>

            @if($flowToSent->busy === 0 && $flowToSent->sent === 0)
            <div>
                <x-alert icon="o-clock" class="alert-info">
                    <p>Será enviado em: <span class="font-bold">
                            {{\Carbon\Carbon::parse($flowToSent->to_sent_at)->format('H:i d/m/Y')}}<span> </p>
                </x-alert>
            </div>

            @elseif($flowToSent->busy === 1 && $flowToSent->sent === 1)
            <div>
                <x-alert icon="o-check" class="alert-success">
                    <p>Enviado as: <span class="font-bold">
                            {{\Carbon\Carbon::parse($flowToSent->updated_at)->format('H:i
                            d/m/Y')}}</span></p>
                </x-alert>

            </div>

            @elseif($flowToSent->busy === 1 && $flowToSent->sent === 0)
            <div>
                <x-alert icon="o-exclamation-triangle" class="alert-wawrning">
                    <p class="font-bold">Em envio...</p>
                </x-alert>
            </div>
            @endif
        </x-card>
        @empty
        <div>
            <div class="text-3xl">Nenhum envio encontrado.</div>
        </div>
        @endforelse
    </div>
</div>