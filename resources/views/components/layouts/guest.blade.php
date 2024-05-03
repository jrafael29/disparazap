<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen h-screen max-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    <div class="flex flex-col">
        <header class="">
            <div class="flex justify-around p-5 ">
                <div class="flex items-center">
                    <a wire:navigate href="{{route('welcome')}}">
                        <x-icon name="o-rocket-launch" />
                    </a>
                </div>
                <div class="flex items-center">
                    @if(Auth::check())
                        <div class="">
                            <h1 class="text-1xl">Bem vindo, <a class="text-blue-700" wire:navigate href="{{route('home')}}"> {{Auth::user()->name}}</a></h1>
                        </div>
                    @else
                        <a wire:navigate href="{{route('login')}}">
                            <x-button class="btn-sm " label="Login" />
                        </a>
                    @endif
                </div>
            </div>
        </header>
        <main class="flex-1 max-h-full">
            {{$slot}}
        </main>

        <footer class="flex justify-center ">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 <a href="https://disparazap.site" class="hover:underline">DisparaZap</a>. Todos os direitos reservados.</span>
        </footer>
    </div>
</body>

</html>