<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Service\InstanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InstanceOpenHandleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private Instance $instance;
    private InstanceService $instanceService;
    public function __construct(
        Instance $instance,
    ) {
        $this->instanceService = App::make(InstanceService::class);
        $this->instance = $instance;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log::info("iniciou o job");
        $instanceData = $this->instanceService->getInstance($this->instance->name);
        if ($instanceData) {
            $instanceData = $instanceData['data'];
            // Log::info("instance data", $instanceData);
            $profilePictureUrlCacheKey = $this->instance->id . "-instance:profilePictureUrl";
            $profileNameCacheKey = $this->instance->id . "-instance:profileName";
            $profileStatusCacheKey = $this->instance->id . "-instance:profileStatus";
            $ttl = env('CACHE_DEFAULT_LIFETIME');
            Cache::add($profilePictureUrlCacheKey, $instanceData['profilePictureUrl'], $ttl);
            Cache::add($profileNameCacheKey, $instanceData['profileName'], $ttl);
            Cache::add($profileStatusCacheKey, $instanceData['profileStatus'], $ttl);
        }

        $this->instance->qrcode_path = '';
        $this->instance->online = true;
        $this->instance->save();
    }
}
