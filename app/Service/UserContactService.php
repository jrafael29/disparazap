<?php

namespace App\Service;

use App\Models\Contact;
use App\Models\UserContact;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserContactService
{
    use ServiceResponseTrait;
    public function createUserContact($userId, $description = null, $phonenumber)
    {
        try {
            DB::beginTransaction();
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
            DB::commit();
            return $this->successResponse([
                'contact' => $contact,
                'userContact' => $userContact
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("error: UserContactService::createUserContact", ['message' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function createManyUserContacts($userId, $phonenumbers = [])
    {
        if (empty($phonenumbers)) return false;
        foreach ($phonenumbers as $phonenumber) {
            $this->createUserContact($userId, uniqid("Contato "), $phonenumber);
        }
        return true;
    }
}
