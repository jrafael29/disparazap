<div>

    @if(Auth::check())
    <h1 class="text-2xl">Bem vindo, {{Auth::user()->name}}</h1>
    <a class="text-blue-800" href="{{route('home')}}">Home</a>

    @else
    <h1>Bem vindo. Faça login para continuar.</h1>
    <a class="text-blue-800" href="{{route('login')}}">Login</a>
    <a class="text-blue-800" href="{{route('register')}}">Registro</a>
    @endif
</div>