<div>
    <p class="text-2xl mb-3">Este usuário possui: {{$wallet->credit}} Créditos</p>
    <div>
        <x-form wire:submit="updateUserCredit">
            <x-input wire:model='giveAmount' label="Créditos" hint="Quantidade de créditos que será adicionada à conta do usuario." inline />
         
            <x-slot:actions>
                <x-button type="reset" label="Limpar" />
                
                <div>
                    <x-modal wire:model="openModal" title="Confirme para continuar" separator>
                        <x-slot:actions>
                            <x-button label="Cancelar" @click="$wire.openModal = false" />
                            <x-button label="Confirmar atualização de saldo" class="btn-primary" type="submit" spinner="updateUserCredit" />
                        </x-slot:actions>
                    </x-modal>
                     
                    <x-button label="Atualizar saldo" @click="$wire.openModal = true" class="btn-primary" />
                </div>

            </x-slot:actions>
        </x-form>
    </div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
</div>
