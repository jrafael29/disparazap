<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;

class Home extends Component
{
    use Toast;

    public string $search = '';

    public bool $drawer = false;

    public string $group = 'group1';

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete($id): void
    {

        $this->warning("Will delete #$id", 'It is fake.', position: 'toast-bottom');
        // $this->redirect('/home');
    }


    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => false],
        ];
    }

    /**
     * For demo purpose, this is a static collection.
     *
     * On real projects you do it with Eloquent collections.
     * Please, refer to maryUI docs to see the eloquent examples.
     */
    public function users(): Collection
    {
        $users = User::query()->get();
        $sortedUsers = $users->sortBy($this->sortBy);
        if ($this->search) {
            $filteredUsers = $sortedUsers->filter(function ($user) {
                return stripos($user->name, $this->search) !== false;
            });
            return $filteredUsers;
        }
        return $sortedUsers;
    }

    public function render()
    {
        return view('livewire.pages.home', [
            'users' => $this->users(),
            'headers' => $this->headers()
        ]);
    }
}
