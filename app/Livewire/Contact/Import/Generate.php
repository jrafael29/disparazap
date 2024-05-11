<?php

namespace App\Livewire\Contact\Import;

use App\Jobs\StorePhonenumbersToVerifyJob;
use App\Models\PhonenumberCheck;
use App\Service\PhonenumberService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Generate extends Component
{
    use Toast;
    public $selectedUf = 'PE';
    #[Rule('gt:0')]
    public $count = 10;
    public $ddi = 55;
    public $ufs = [
        ['id' => '1', 'name' => 'AC', 'ddds' => [68]],
        ['id' => '2', 'name' => 'AL', 'ddds' => [82]],
        ['id' => '3', 'name' => 'AM', 'ddds' => [92, 97]],
        ['id' => '4', 'name' => 'AP', 'ddds' => [96]],
        ['id' => '5', 'name' => 'BA', 'ddds' => [71, 73, 74, 75, 77]],
        ['id' => '6', 'name' => 'CE', 'ddds' => [85, 88]],
        ['id' => '7', 'name' => 'DF', 'ddds' => [61]],
        ['id' => '8', 'name' => 'ES', 'ddds' => [27, 28]],
        ['id' => '9', 'name' => 'GO', 'ddds' => [61, 62, 64]],
        ['id' => '10', 'name' => 'MA', 'ddds' => [98, 99]],
        ['id' => '11', 'name' => 'MG', 'ddds' => [31, 32, 33, 34, 35, 37, 38]],
        ['id' => '12', 'name' => 'MS', 'ddds' => [67]],
        ['id' => '13', 'name' => 'MT', 'ddds' => [65, 66]],
        ['id' => '14', 'name' => 'PA', 'ddds' => [91, 93, 94]],
        ['id' => '15', 'name' => 'PB', 'ddds' => [83]],
        ['id' => '16', 'name' => 'PE', 'ddds' => [81, 87]],
        ['id' => '17', 'name' => 'PI', 'ddds' => [86, 89]],
        ['id' => '18', 'name' => 'PR', 'ddds' => [41, 42, 43, 44, 45, 46]],
        ['id' => '19', 'name' => 'RJ', 'ddds' => [21, 22, 24]],
        ['id' => '20', 'name' => 'RN', 'ddds' => [84]],
        ['id' => '21', 'name' => 'RO', 'ddds' => [69]],
        ['id' => '22', 'name' => 'RR', 'ddds' => [95]],
        ['id' => '23', 'name' => 'RS', 'ddds' => [51, 53, 54, 55]],
        ['id' => '24', 'name' => 'SC', 'ddds' => [42, 47, 48, 49]],
        ['id' => '25', 'name' => 'SE', 'ddds' => [79]],
        ['id' => '26', 'name' => 'SP', 'ddds' => [11, 12, 13, 14, 15, 16, 17, 18, 19]],
        ['id' => '27', 'name' => 'TO', 'ddds' => [63]],
    ];

    public $openModal = false;

    private PhonenumberService $phonenumberService;

    public function generate()
    {
        $this->validate();

        $selectedUfDdds = collect($this->ufs)->where('name', $this->selectedUf)->first()['ddds'];
        $phonenumbers = $this->phonenumberService->generatePhonenumbersForManyDdds(
            ddi: $this->ddi,
            ddds: $selectedUfDdds,
            count: $this->count
        );

        $userId = Auth::user()->id;
        $check = PhonenumberCheck::create([
            'user_id' => $userId,
            'description' => "Checagem dos números gerados para o uf: {$this->selectedUf}"
        ]);
        StorePhonenumbersToVerifyJob::dispatch(
            $userId,
            $check->id,
            $phonenumbers
        )->onQueue('default');
        $this->info("Os números gerados serão verificados em minutos.");
        $this->redirect('/verify', navigate: true);
    }

    public function boot(PhonenumberService $phonenumberService)
    {
        $this->phonenumberService = $phonenumberService;
    }

    public function render()
    {
        return view('livewire.contact.import.generate');
    }
}
