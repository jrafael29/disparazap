<?php

namespace App\Livewire\Sent;

use App\Models\Sent;
use App\Service\SentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast;
    public $expanded = [];
    public $headers = [
        ['key' => 'id', 'label' => '#', 'class' => 'hidden'],
        ['key' => 'description', 'label' => 'Description'],
    ];

    private SentService $sentService;

    public function pauseSent(Sent $sent)
    {
        $resultPause = $this->sentService->pauseSent($sent->id);
        if (!$resultPause) {
            return false;
        }
        $this->success("Envio pausado com sucesso.");
    }

    public function playSent(Sent $sent)
    {
        $resultPlay = $this->sentService->playSent($sent->id);
        if (!$resultPlay) {
            return false;
        }
        $this->success("Envio retomado com sucesso.");
    }

    public function sents()
    {
        $sents = Sent::query()
            ->with(['flows'])
            ->where('user_id', Auth::user()->id)
            ->get();
        return $sents;
    }

    public function boot(SentService $sentService)
    {
        $this->sentService = $sentService;
    }

    public function render()
    {
        return view('livewire.sent.table', [
            'sents' => $this->sents()
        ]);
    }
}
