<div>
    <div class="mb-3">
        <a wire:navigate href="{{route('admin.user')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>
    <!-- HEADER -->
    <x-header title="{{$user->name}}" subtitle="{{$user->email}}" separator
        progress-indicator>

    </x-header>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div>
        <livewire:admin.user.wallet.index :user="$user" />
    </div>
</div>
