<?php

namespace App\Service;

use App\Helpers\Phonenumber;
use App\Models\Contact;
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
                    dd("oxe", $userContact);
                    continue;
                }

                // verifica se o contato ja está no grupo
                $groupHasContact = UserContactGroup::query()
                    ->where('user_contact_id', $userContact->id)
                    ->where('user_group_id', $groupId)
                    ->first();

                if ($groupHasContact) {
                    dd("oxe", $groupHasContact);
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

    public function addContactToGroup($userId, $groupId, $phonenumber)
    {
        try {

            if (!$userId || !$groupId || !$phonenumber) return $this->errorResponse("invalid parameters");

            // pegar o contato
            $ddiAndDdd = substr($phonenumber, 0, 4);
            $phone = Phonenumber::lastEightDigits($phonenumber);
            $contact = Contact::query()
                ->where('phonenumber', 'like', $ddiAndDdd . '%')
                ->where('phonenumber', 'like', '%' . $phone)
                ->first();

            if (!$contact) {
                return;
            }

            $userContact = UserContact::query()->where('contact_id', $contact->id)->first();

            if (!$userContact) {
                // usuario não tem ligação com o numero;
                $userContact = UserContact::query()->create([
                    'user_id' => $userId,
                    'contact_id' => $contact->id
                ]);
            }

            $contactAlreadyInGroup = UserContactGroup::query()
                ->where('user_contact_id', $userContact->id)
                ->where('user_group_id', $groupId)
                ->first();

            if (!$contactAlreadyInGroup) {
                $contactAlreadyInGroup = UserContactGroup::query()
                    ->create([
                        'user_contact_id' => $userContact->id,
                        'user_group_id' => $groupId
                    ]);
            }

            return $this->successResponse([
                'success' => true
            ]);
            // $userContact = UserContact::query()->whereHas('contact', function ($query) use ($phonenumber) {
            //     $ddiAndDdd = substr($phonenumber, 0, 4);
            //     $phone = Phonenumber::lastEightDigits($phonenumber);
            //     $query
            //         ->where('phonenumber', 'like', $ddiAndDdd . '%')
            //         ->where('phonenumber', 'like', '%' . $phone);
            // })
            //     ->where('user_id', $userId)
            //     ->first();

            // if (!$userContact) {
            //     UserContact::query()->create([

            //         'phonenumber' => $phonenumber
            //     ])
            // return $this->errorResponse('contact not belong to user');



        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
