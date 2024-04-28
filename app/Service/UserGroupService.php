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
                    $ddiAndDdd = substr($phonenumber, 0, 4);
                    $phone = Phonenumber::lastEightDigits($phonenumber);
                    $query
                        ->where('phonenumber', 'like', $ddiAndDdd . '%')
                        ->where('phonenumber', 'like', '%' . $phone);
                })
                    ->where('user_id', $userId)
                    ->first();

                if (!$userContact) {
                    continue;
                }

                // verifica se o contato ja está no grupo

                $groupHasContact = UserContactGroup::query()->where('user_contact_id', $userContact->id)->first();

                if ($groupHasContact) {
                    continue;
                }
                // se nao tiver insere.

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
