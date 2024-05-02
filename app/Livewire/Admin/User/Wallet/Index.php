<?php

namespace App\Livewire\Admin\User\Wallet;

use App\Models\User;
use App\Models\UserWallet;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class Index extends Component
{

    use Toast;

    public $wallet;
    public int $giveAmount = 0;

    public bool $openModal = false;

    public function mount(User $user)
    {
        $this->wallet = $user->wallet;
    }

    public function updateUserCredit()
    {

        if ($this->giveAmount < 1 || $this->giveAmount > 9999) {
            $this->error("Valor inválido");
            $this->openModal = false;
            return false;
        }

        $id = $this->wallet->id;
        UserWallet::query()->where('id', $id)->increment('credit', $this->giveAmount);
        $this->reset('giveAmount', 'openModal');
        $this->dispatch('update-wallet');
        $this->success("Saldo atualizado com sucesso.");
    }

    #[On('update-wallet')]
    public function render()
    {
        return view('livewire.admin.user.wallet.index');
    }
}
