<div>
    <div class="mb-3">
        <a wire:navigate href="{{route('contact')}}">
            <x-button class="btn-outline">
                Voltar
            </x-button>
        </a>
    </div>
    <!-- HEADER -->
    <x-header title="Grupos de contatos" subtitle="Adicione contatos a seus grupos" separator
        progress-indicator>
        <x-slot:actions>

            <div>
                <livewire:contact.group.form-modal />
            </div>

        </x-slot:actions>
    </x-header>

    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}

    <div>
        @if(count($groups))
        <div class="h-96 overflow-y-auto">
            <x-table :headers="$headers" :rows="$groups" wire:model="expanded" expandable>
                @scope('cell_identifier', $group)
                #
                @endscope

                @scope('cell_contactsCount', $group)
                {{$group->userContacts->count()}}
                @endscope

                @scope('expansion', $group)
                <div>
                    <livewire:contact.group.card  :group="$group" />
                </div>
                @endscope
            </x-table>

        </div>
        @else   
        <x-alert title="Você ainda não cadastrou nenhum grupo." icon="o-exclamation-triangle"
                    class="alert-warning" shadow/>

        @endif
    </div>
</div>