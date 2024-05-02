<div class="flex items-center justify-center">
    <div class="sm:w-1/3 md:2/3 ">
        <div class="mb-5">
            <h1 class="text-center text-4xl">Login</h1>
        </div>
        <div class="mb-5">
            <x-form wire:submit="handleSubmit">
                <x-input label="Email" type="email" wire:model="email" placeholder="janedoe@mail.com" />
                <x-input label="Senha" type="password" wire:model="password" placeholder="******" />

                <x-slot:actions>
                    <x-button label="Limpar" type="reset" />
                    <x-button label="Login" class="btn-primary" type="submit" spinner="save" />
                </x-slot:actions>
            </x-form>

        </div>
        {{-- <div>
            <p class="text-1xl">Não tem uma conta? <a class="text-blue-900"
                    href="{{route('register')}}">Registre-se</a>.</p>
        </div> --}}
        {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    </div>
</div>