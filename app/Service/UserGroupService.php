<?php

namespace App\Service;

use App\Helpers\Phonenumber;
use App\Models\UserContact;
use App\Models\UserContactGroup;
use App\Models\UserGroup;
use App\Traits\ServiceResponseTrait;

class UserGroupService
{
    use ServiceResponseTrait;

    public function createGroup($userId, $name, $description = null)
    {
        if (empty($userId) || empty($name)) return false;
        $group = UserGroup::query()->create([
            'user_id' => $userId,
            'name' => $name,
            'description' => $description
        ]);
        return $this->successResponse([
            'group' => $group
        ]);
    }

    public function addContactsToGroup($userId, $groupId, $contacts = [])
    {
        try {

            if (empty($contacts)) return $this->errorResponse("invalid parameters");
            foreach ($contacts as $phonenumber) {
                $userContact = UserContact::query()->whereHas('contact', function ($query) use ($phonenumber) {
                    $ph = Phonenumber::lastEightDigits($phonenumber);
                    $query->where('phonenumber', "like", '%' . $ph . '%');
                })
                    ->where('user_id', $userId)
                    ->first();

                if (!$userContact) {
                    continue;
                }
                UserContactGroup::query()->create([
                    'user_group_id' => $groupId,
                    'user_contact_id' => $userContact->id
                ]);
            }
            return $this->successResponse([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
