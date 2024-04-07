<?php

namespace App\Service;

use App\Models\Contact;
use App\Models\UserContact;
use App\Traits\ServiceResponseTrait;

class UserContactService
{
    use ServiceResponseTrait;
    public function createUserContact($userId, $description = null, $phonenumber)
    {
        try {
            $contact = Contact::query()->firstOrCreate([
                'phonenumber' => $phonenumber
            ], [
                'phonenumber' => $phonenumber,
                'description' => $description
            ]);
            $userContact = UserContact::query()->firstOrCreate([
                'user_id' => $userId,
                'contact_id' => $contact->id
            ]);

            return $this->successResponse([
                'contact' => $contact,
                'userContact' => $userContact
            ]);
        } catch (\Exception $e) {
            report($e->getMessage());
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function storeManyUserContacts($userId, $phonenumbers = [])
    {
        if (empty($phonenumbers)) return false;
        foreach ($phonenumbers as $phonenumber) {
            $this->createUserContact($userId, uniqid("Contato "), $phonenumber);
        }
        return true;
    }
}
