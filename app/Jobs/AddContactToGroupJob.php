<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserGroup;
use App\Service\UserGroupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddContactToGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public UserGroup $userGroup,
        public $phonenumber
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(UserGroupService $userGroupService): void
    {
        Log::info("init AddContactToGroupJob");
        $result = $userGroupService->addContactToGroup(
            userId: $this->user->id,
            groupId: $this->userGroup->id,
            phonenumber: $this->phonenumber
        );
        Log::info("end AddContactToGroupJob", [
            'result' => $result
        ]);
    }
}
