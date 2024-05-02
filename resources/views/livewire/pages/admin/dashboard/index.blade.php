<div class="">
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="flex flex-col gap-5">
        <div class="flex gap-5">
            <x-stat description="Disparos agendados / pendentes" value="{{$scheduledCount}}" icon="o-clock" tooltip="Hello" />
            <x-stat description="Disparos finalizados" value="{{$doneCount}}" icon="o-check" tooltip="Hello" />
            <x-stat description="Disparos em andamento" value="{{$inProgressCount}}" icon="o-fire" tooltip="Hello" />
            <x-stat description="Disparos pausados" value="{{$pausedCount}}" icon="o-pause" tooltip="Hello" />
        </div>
    
        <div class="flex gap-5">
            <x-stat description="Mensagens enviadas" value="{{$messagesCount}}" icon="o-envelope" tooltip="Hello" />
            <x-stat description="Contatos" value="{{$contactsCount}}" icon="o-user-plus" tooltip="Hello" />
            <x-stat description="Grupos" value="{{$groupsCount}}" icon="o-user-group" tooltip="Hello" />
        </div>
    
    </div>

</div>
