<?php

namespace App\Jobs;

use App\Models\FlowToSent;
use App\Models\MessageFlow;
use App\Service\Evolution\EvolutionSendMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class SendMessageFlowToTargetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public EvolutionSendMessageService $messageService;
    /**
     * Create a new job instance.
     */
    public function __construct(private FlowToSent $flowToSent)
    {
        $this->messageService = App::make(EvolutionSendMessageService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messages = $this->flowToSent->flow->messages;
        if (empty($messages)) return;
        foreach ($messages as $message) {
            $instance = $this->flowToSent->instance;
            switch ($message->type->name) {
                case 'image':
                    $image = public_path('storage/' . $message->filepath);
                    $base64 = base64_encode(file_get_contents($image));
                    $this->messageService->sendImage(
                        instanceName: $instance->name,
                        imageBase64OrUrl: $base64,
                        text: $message->text,
                        to: $this->flowToSent->to,
                        delay: ($message->delay * 1000) // in ms
                    );
                    break;
                case 'video':
                    $video = public_path('storage/' . $message->filepath);
                    $base64 = base64_encode(file_get_contents($video));
                    $this->messageService->sendVideo(
                        instanceName: $instance->name,
                        videoBase64OrUrl: $base64,
                        text: $message->text,
                        to: $this->flowToSent->to,
                        delay: ($message->delay * 1000) // in ms
                    );
                    break;

                case 'text':
                    $this->messageService->sendText(
                        instanceName: $instance->name,
                        text: $message->text,
                        to: $this->flowToSent->to,
                        delay: ($message->delay * 1000) // in ms
                    );
                    break;
            }
            sleep(1); // 1 segundo entre uma mensagem e outra.
        }
        $delayBetweenChats = $this->flowToSent->delay_in_seconds ?? 15; // 15 segundos entre um chat e outro.
        sleep((int)$delayBetweenChats);
        $this->flowToSent->sent = 1;
        $this->flowToSent->save();
    }
}
