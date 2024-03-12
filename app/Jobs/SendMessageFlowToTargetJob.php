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
use Illuminate\Support\Facades\Log;

class SendMessageFlowToTargetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private EvolutionSendMessageService $messageService;
    private FlowToSent $flowToSent;

    /**
     * Create a new job instance.
     */
    public function __construct(FlowToSent $flowToSent)
    {
        $this->messageService = App::make(EvolutionSendMessageService::class);
        $this->flowToSent = $flowToSent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messages = $this->flowToSent->flow->messages;
        Log::alert('starting send message');
        Log::alert($messages);
        if (empty($messages)) return;
        try {
            $instance = $this->flowToSent->instance;
            foreach ($messages as $message) {
                $delayInMs = ($message->delay * 1000);
                switch ($message->type->name) {
                    case 'image':
                        $image = public_path('storage/' . $message->filepath);
                        $base64 = base64_encode(file_get_contents($image));
                        $this->messageService->sendImage(
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
                        $this->messageService->sendVideo(
                            instanceName: $instance->name,
                            videoBase64OrUrl: $base64,
                            text: $message->text,
                            to: $this->flowToSent->to,
                            delay: $delayInMs // in ms
                        );
                        break;

                    case 'text':
                        $this->messageService->sendText(
                            instanceName: $instance->name,
                            text: $message->text,
                            to: $this->flowToSent->to,
                            delay: $delayInMs // in ms
                        );
                        break;
                }
                sleep(1); // 1 segundo entre uma mensagem e outra.
            }
            // $delayBetweenChats = $this->flowToSent->delay_in_seconds ?? 15; // 15 segundos entre um chat e outro.
            $delayBetweenChats = 5;
            sleep((int)$delayBetweenChats);
            $this->flowToSent->sent = 1;
            $this->flowToSent->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
