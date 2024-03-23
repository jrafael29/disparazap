<div>
    <div>
        <x-header title="Gerenciamento de usuários" subtitle="Aqui você pode gerenciar os usuarios cadastrados."
            separator progress-indicator>
        </x-header>
    </div>
    {{-- Do your work, then step back. --}}
    <h1>pagina de gerenciamento de usuarios</h1>


    <x-table :headers="$headers" :rows="$users">

        {{-- Notice `$user` is the current row item on loop --}}
        @scope('cell_id', $user)
        <strong>{{ $user->id }}</strong>
        @endscope

        {{-- You can name the injected object as you wish --}}
        @scope('cell_name', $stuff)
        <x-badge :value="$stuff->name" class="badge-info" />
        @endscope

        {{-- Special `actions` slot --}}
        @scope('actions', $user)
        <x-button icon="o-trash" wire:click="delete({{ $user->id }})" spinner class="btn-sm" />
        <x-modal id="modal-{{$user->id}}" title="#{{$user->id}} - {{$user->name}}" subtitle="{{$user->email}}">
            <div>Usuario ID {{$user->id}}</div>
            <div class="bg-red-500">
                <x-tabs selected="users-tab">
                    <x-tab name="info-tab" label="Informações" icon="o-users">
                        <div>Informações básicas</div>
                    </x-tab>
                    <x-tab name="credit-tab" label="Carteira" icon="o-sparkles">
                        <div>Crédito</div>
                    </x-tab>
                    <x-tab name="musics-tab" label="Fluxos" icon="o-musical-note">
                        <div>Disparo</div>
                    </x-tab>
                </x-tabs>
            </div>
            <x-slot name="actions">
                {{-- Buttons should be inside the actions slot --}}
                <x-button label="Cancel" onclick="document.getElementById('modal-{{$user->id}}').close()" />
                <x-button label="Confirm" class="btn-primary" />
            </x-slot>
        </x-modal>

        {{-- Button to open modal --}}
        <x-button label="Open modal" class="btn-primary"
            onclick="document.getElementById('modal-{{$user->id}}').showModal()" />
        @endscope

    </x-table>
</div>