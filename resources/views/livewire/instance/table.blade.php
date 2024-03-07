<div wire:poll class="flex gap-5 flex-wrap">


    @foreach($instances as $instance)

    <livewire:instance.card wire:key='{{$instance->id}}' :instance="$instance" />
    @endforeach


</div>