<div class="flex gap-5 flex-wrap">


    @forelse($instances as $instance)

    <livewire:instance.card wire:key='{{$instance->id}}' :instance="$instance" />
    @empty
    <div>
        <p class="text-3xl">Nenhuma instância encontrada.</p>
    </div>
    @endforelse


</div>