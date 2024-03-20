<?php

namespace App\Jobs;

use App\Models\FlowToSent;
use App\Models\Instance;
use App\Models\MessageFlow;
use App\Service\Evolution\EvolutionSendMessageService;
use Carbon\Carbon;
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
        // Log::alert('starting send message');
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
                        Log::alert('init send text');
                        $this->messageService->sendText(
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
            $this->flowToSent->sent = 1;
            $this->flowToSent->save();


            $delayInSeconds = $this->flowToSent->delay_in_seconds ?? 15; // 15 segundos entre um chat e outro.

            $availableAt = Carbon::now()->addSeconds($delayInSeconds);

            $instance = Instance::find($this->flowToSent->instance_id);
            $instance->available_at = $availableAt;
            $instance->save();
        } catch (\Exception $e) {
            Log::error("erro job:sendmessage= ", $e->getMessage());
        }
    }
}
