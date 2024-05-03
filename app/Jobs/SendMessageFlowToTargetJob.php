<?php

namespace App\Jobs;

use App\Models\FlowToSent;
use App\Models\Instance;
use App\Models\MessageFlow;
use App\Service\Evolution\EvolutionSendMessageService;
use App\Service\UserWalletService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendMessageFlowToTargetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public FlowToSent $flowToSent)
    {
    }

    public function handle(
        EvolutionSendMessageService $messageService,
        UserWalletService $userWalletService
    ): void {
        $this->flowToSent->busy = 1;
        $this->flowToSent->save();

        if (!$this->flowToSent->sent->started) {
            // marcar o disparo como iniciado (SENT);
            $this->flowToSent->sent->update(['started' => 1]);
        }

        $messages = $this->flowToSent->flow->messages;

        Log::alert('init send message');
        if (empty($messages)) return;
        try {
            $instance = $this->flowToSent->instance;
            foreach ($messages as $message) {
                // Log::alert($message);

                $delayInMs = ($message->delay * 1000);
                // Log::alert($message->type->name);
                switch ($message->type->name) {
                    case 'image':
                        $image = public_path('storage/' . $message->filepath);
                        $base64 = base64_encode(file_get_contents($image));
                        $messageService->sendImage(
                            instanceName: $instance->name,
                            imageBase64OrUrl: $base64,
                            text: $message->text,
                            to: $this->flowToSent->to,
                            delay: $delayInMs // in ms
                        );
                        break;
                    case 'video':
                        $video = public_path('storage/' . $message->filepath);
                        $base64 = base64_encode(file_get_contents($video));
                        $messageService->sendVideo(
                            instanceName: $instance->name,
                            videoBase64OrUrl: $base64,
                            text: $message->text,
                            to: $this->flowToSent->to,
                            delay: $delayInMs // in ms
                        );
                        break;

                    case 'text':
                        Log::alert('init send text');
                        $messageService->sendText(
                            instanceName: $instance->name,
                            text: $message->text,
                            to: $this->flowToSent->to,
                            delay: $delayInMs // in ms
                        );
                        break;
                }
                if (count($messages) > 1) {
                    sleep(1); // 1 segundo entre uma mensagem e outra.
                }
            }
            // DB::beginTransaction();
            $this->flowToSent->sent = 1;
            $this->flowToSent->save();

            // se ja foi enviado cobra
            $userWalletService->debitOne(
                userId: $this->flowToSent->user_id,
                description: "Cobrança referente ao envio do fluxo: (" . $this->flowToSent->flow->description . ") para o numero: (" . $this->flowToSent->to . ") na data: (" . now()->format('d/m/Y H:i') . ")"
            );

            $delayInSeconds = $this->flowToSent->delay_in_seconds ?? 15; // 15 segundos entre um chat e outro.

            $availableAt = Carbon::now()->addSeconds($delayInSeconds);
            $instance = Instance::find($this->flowToSent->instance_id);
            $instance->available_at = $availableAt;
            $instance->save();
            // DB::commits();
        } catch (\Exception $e) {
            // DB::rollBack();
            Log::error("error: SendMessageFlowToTargetJob", ['message' => $e->getMessage()]);
        }
    }
}
