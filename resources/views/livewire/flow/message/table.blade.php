<div>
    <div class="mb-4">
        @if(count($messages))
        <div>
            <x-alert title="Neste icone você pode arrastar as mensagens para alterar a ordem de
            envio." description="Será enviado em ordem da esquerda para a direita. " icon="m-cursor-arrow-rays" shadow>
            </x-alert>
        </div>

        @endif
    </div>
    <div class="flex justify-center sm:justify-start gap-10 flex-wrap " x-init="Sortablejs.create($el, {
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
                    <x-icon name="m-cursor-arrow-rays" class="cursor-pointer" />

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
        <div class="w-full">
            <x-alert title="Ops... Nenhuma mensagem encontrada."
                description="Crie uma ou mais mensagens para depois envia-la para seus contatos."
                icon="o-exclamation-triangle" class="alert-warning">
            </x-alert>
        </div>
        @endforelse
    </div>
</div>