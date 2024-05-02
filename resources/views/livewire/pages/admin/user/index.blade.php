<div>
    <div>
        <x-header title="Gerenciamento de usuários" subtitle="Aqui você pode gerenciar os usuarios cadastrados."
            separator progress-indicator>
            <x-slot:actions>
                <livewire:admin.user.create />
            </x-slot:actions>
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
            <a wire:navigate href="{{route('admin.user.show', ['id' =>$user->id])}}">
                <x-button icon="o-pencil-square" tooltip-left="editar" class="btn-sm btn-outline"/>
            </a>
        @endscope

    </x-table>
</div>