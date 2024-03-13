<div class="flex justify-center items-center h-screen">
    <div class="text-center">
        @if(Auth::check())
        <div class="mb-3">
            <h1 class="text-2xl">Bem vindo, {{Auth::user()->name}}</h1>
        </div>
        <div>
            <a class="text-blue-800" href="{{route('home')}}">Ir à Home</a>
        </div>

        @else
        <div class="mb-5">
            <h1>Bem vindo! <br> Faça login para continuar.</h1>
        </div>
        <div class="flex gap-5 justify-center">
            <div class="mb-5">
                <a class="text-blue-800" href="{{route('register')}}">
                    <x-button class="btn-outline">Registro</x-button>
                </a>
            </div>
            <div class="mb-5">
                <a class="text-blue-800" href="{{route('login')}}">
                    <x-button class="btn-primary">Login</x-button>
                </a>
            </div>
        </div>
        @endif
    </div>

</div>