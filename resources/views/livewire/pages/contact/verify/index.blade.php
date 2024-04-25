<div wire:poll.4s>

    <x-header title="Verificações de existência" subtitle="Todas as verificações de números." separator progress-indicator>
    </x-header>
    {{-- Stop trying to control. --}}

    <x-table :headers="$headers" :rows="$verifies" wire:model="expanded" striped with-pagination>

        @scope('cell_description', $verify)
        <div class=" flex justify-between">
            <div class=" flex-1">
                {{$verify->description}}
            </div>
            <div class=" flex-1">
                <span class="text-blue-500"><a href="{{route('verify.show', ['id'=>$verify->id])}}">Ver</a></span>
            </div>
        </div>
        @endscope
        @scope('cell_count', $verify)
        {{$verify->verifies->count()}}
        @endscope
        @scope('cell_verified', $verify)
        {{$verify->verifies->where('verified', 1)->count()}}
        @endscope
        @scope('cell_existents', $verify)
        {{$verify->verifies->where('verified', 1)->where('isOnWhatsapp', 1)->count()}}
        @endscope

        @scope('cell_done', $verify)
        @if($verify->done)
        <x-icon name="o-check" class="w-8 h-8 bg-green-500 text-white p-2 rounded-full" />
        @else
        <x-loading class="text-primary" />
        @endif
        @endscope
        @scope('cell_created_at', $verify)
            {{$verify->created_at->diffForHumans()}}
        @endscope

    </x-table>
</div>
