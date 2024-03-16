<div class="flex gap-5 flex-wrap justify-center lg:justify-start">


    @forelse($instances as $instance)

    <livewire:instance.card wire:key='{{$instance->id}}' :instance="$instance" />
    @empty
    <div class="w-full">
        <x-alert title="Ops... Nenhuma instância encontrada."
            description="Crie uma instância do whatsapp para continuar" icon="o-exclamation-triangle"
            class="alert-warning">
        </x-alert>
    </div>
    @endforelse


</div>