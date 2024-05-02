<?php

namespace App\Livewire\Pages\Admin\Dashboard;

use App\Models\FlowToSent;
use App\Models\Sent;
use App\Models\UserContact;
use App\Models\UserGroup;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {

        $scheduledCount = Sent::where('start_at', '>', now()->subSecond())->count();;
        $doneCount = Sent::where('done', 1)->count();
        $inProgressCount = Sent::where('paused', 0)->where('started', 1)->count();
        $pausedCount = Sent::where('paused', 1)->count();

        // Sent::where('start_at', '>', now()->subSecond())->count();

        // UserContact::count();
        // FlowToSent::where('sent', 1)->count();

        $messagesCount = FlowToSent::where('sent', 1)->count();;
        $contactsCount = UserContact::count();
        $groupsCount = UserGroup::count();

        return view('livewire.pages.admin.dashboard.index', [
            'scheduledCount' => $scheduledCount,
            'doneCount' => $doneCount,
            'inProgressCount' => $inProgressCount,
            'inProgressCount' => $inProgressCount,
            'pausedCount' => $pausedCount,
            'messagesCount' => $messagesCount,
            'contactsCount' => $contactsCount,
            'groupsCount' => $groupsCount,
        ]);
    }
}
