<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-base-100">

            {{-- BRAND --}}
            {{--
            <x-app-brand class="p-5 pt-3" /> --}}

            {{-- MENU --}}
            <x-menu activate-by-route>
                {{-- User --}}
                @if($user = auth()->user())
                <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover
                    class="mb-5 -mx-2 rounded">
                    <x-slot:actions>
                        <div class="flex gap-3 items-center justify-center">
                            <x-button icon="o-power" class="btn btn-circle btn-ghost btn-xs" tooltip-left="logoff"
                                no-wire-navigate link="/logout" />
                            <x-theme-toggle class="btn btn-circle" />
                            <x-button icon="o-currency-dollar" class="btn btn-circle btn-ghost btn-xs" tooltip-left="Créditos: {{$user->wallet->credit}}"/>
                        </div>
                    </x-slot:actions>
                </x-list-item>
                <x-menu-separator />
                <x-menu-item title="Inicio" icon="o-home" link="{{route('home')}}" />
                <x-menu-item title="Conectar WhatsApp" icon="o-inbox-stack" link="{{route('instance')}}" />                
                @can('have-online-instances')
                <x-menu-sub title="Contatos" icon="o-users" >
                    <x-menu-item title="Visualização" icon="o-list-bullet" link="{{route('contact')}}" />
                    <x-menu-item title="Grupos de contatos" icon="o-user-group" link="{{route('groups')}}" />
                    <x-menu-item title="Importar" icon="o-arrow-down-tray" link="{{route('import')}}" />
                    <x-menu-item title="Verificações de existência" icon="o-clock" link="{{route('verify')}}" />
                </x-menu-sub>

                
                <x-menu-sub title="Envios" icon="o-paper-airplane">
                    <x-menu-item title="Visualização" icon="o-list-bullet" link="{{route('sent')}}" />
                    <x-menu-item title="Fluxo de Mensagens" icon="o-chat-bubble-oval-left-ellipsis"
                        link="{{route('flow')}}" />
                    @endcan
                </x-menu-sub>

                {{--
                <x-menu-item title="Utilitário" icon="o-code-bracket-square" link="{{route('extractor')}}" /> --}}
                @if(Auth::user()->isAdmin)
                <x-menu-item title="Bonus" icon="o-gift" />
                {{--
                <x-menu-item title="Usuários" link="{{route('admin.user')}}" icon="o-user" /> --}}

                @endif

                @endif
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{-- TOAST area --}}
    <x-toast />
</body>

</html>