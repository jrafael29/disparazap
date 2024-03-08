<div>
    <div class="mb-4">
        <h1 class="text-2xl">No icone você pode arrastar as mensagens para alterar a ordem de
            envio.
        </h1>
        <small>Da esquerda pra direita. </small>
    </div>
    <div class="flex gap-10 flex-wrap text-white " x-init="Sortablejs.create($el, {
    animation: 150,
    handle: '.cursor-pointer',
    onSort({to}){
        const flowId = {{$this->flow->id}}
        const ids = Array.from(to.children).map(item => item.getAttribute('message-id'))
        console.log('flowId', flowId)
        @this.reOrderMessages(ids, flowId)
    }
})">

        {{-- Care about people's approval and you will be their prisoner. --}}
        @forelse($messages as $message)

        <div wire:key='{{$message->id}}' message-id="{{$message->id}}">

            <x-card class="w-64" title="{{$message->type->description}}"
                subtitle="{{$message->delay}} segundos digitando..." shadow separator>
                <x-slot:menu>

                    <x-button icon="o-trash" type="button" wire:click='handleDeleteMessageClick({{$message->id}})'
                        class="btn-outline text-red-500 btn-sm" />
                    <x-icon name="o-arrows-pointing-in" class="cursor-pointer" />

                </x-slot:menu>


                <div>

                    @if($message->text)
                    <div class="mb-3">
                        <b>Texto da mensagem:</b>
                        {{$message->text}}
                    </div>
                    @endif

                    @if($message->filepath)
                    @switch($message->type->name)
                    @case('video')
                    <video width="320" height="240" controls>
                        <source src="{{asset('storage/'.$message->filepath)}}" type="video/mp4">
                    </video>
                    @break;
                    @case('image')
                    <img width="320" src="{{asset('storage/'.$message->filepath)}}" alt="oxe">
                    @break;
                    @endswitch
                    @endif

                </div>
            </x-card>

        </div>

        @empty
        <p class="text-2xl text-black ">
            Ops... Parece que você ainda não adicionou nenhuma mensagem a este
            fluxo.
        </p>

        @endforelse
    </div>
</div>