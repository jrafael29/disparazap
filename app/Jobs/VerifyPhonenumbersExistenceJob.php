<?php

namespace App\Jobs;

use App\Helpers\ArrayHelper;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyPhonenumbersExistenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phonenumbers = [];
    public User $user;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $phonenumbers = [])
    {
        $this->user = User::query()->find($userId);
        $this->phonenumbers = $phonenumbers;
        // phonenumbers 100000 length
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // $userInstances = $this->user->instances;

        $userInstances = Instance::query()
            ->whereHas('user', function ($q) {
                $q->where('active', 1);
            })
            ->where('user_id', $this->user->id)
            ->where('online', 1)
            ->get();

        $phonenumbersByInstance = ArrayHelper::divideArrayItems(
            items: $this->phonenumbers,
            itemsPerBatch: (count($this->phonenumbers) / $userInstances->count())
        );
        dd("phonenumbersByInstance", $phonenumbersByInstance);
        foreach ($userInstances as $key => $userInstance) {
            $instancePhonenumberBatch = $phonenumbersByInstance[$key];

            // para cada instancia
            // um job

            // VerifiedPhonenumber
            // verified_phonenumbers

            // ID | user_id | phonenumber   | verified | inOnWhatsapp
            // 1  | 1       | 5581991931921 | true     | true
            // 2  | 1       | 5581991931222 | true     | false
            // 3  | 1       | 5581991931321 | false    | false


            dd("batch", $instancePhonenumberBatch);
        }
    }
}
