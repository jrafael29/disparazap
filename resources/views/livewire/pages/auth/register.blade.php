<div class="flex justify-center">
    <div class="sm:w-1/3 md:2/3">
        <div class="mb-5">
            <h1 class="text-center text-4xl">Cadastro</h1>
        </div>
        <div class="">
            <x-form wire:submit="handleSubmit">
                <x-input label="Nome" type="name" wire:model="name" placeholder="Jane Doe" />
                <x-input label="Email" type="email" wire:model="email" placeholder="jane@doe.com" />
                <x-input label="Senha" type="password" wire:model="password" placeholder="******" />
                <x-input label="Confirmar senha" type="password" wire:model="password_confirmation"
                    placeholder="******" />


                <x-slot:actions>
                    <x-button label="Limpar" type="reset" />
                    <x-button label="Cadastrar" class="btn-primary" type="submit" spinner="save" />
                </x-slot:actions>
            </x-form>
            <p class="text-1xl">Já tem uma conta? <a class="text-blue-900" href="{{route('login')}}">Faça login</a>.</p>
        </div>
        {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    </div>
</div>