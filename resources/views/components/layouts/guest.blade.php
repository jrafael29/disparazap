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
    <div class="flex justify-around p-5 mb-10">
        
        <div class="flex items-center">
            <a wire:navigate href="{{route('welcome')}}">
                <x-icon name="o-rocket-launch" />
            </a>
        </div>
        <div class="">
            @if(Auth::check())
                <div class="mb-3">
                    <h1 class="text-1xl">Bem vindo, <a class="text-blue-400" wire:navigate href="{{route('home')}}"> {{Auth::user()->name}}</a></h1>
                </div>
            @else
                <a wire:navigate href="{{route('login')}}">
                    <x-button class="btn-sm " label="Login" />
                </a>
            @endif
        </div>
    </div>

    {{$slot}}
    {{-- MAIN --}}
    {{-- TOAST area --}}
    <footer>
        <div class="pb-5 flex justify-center">
            <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 <a href="https://flowbite.com/" class="hover:underline">DisparaZap</a>. Todos os direitos reservados.</span>
        </div>
    </footer>
</body>

</html>