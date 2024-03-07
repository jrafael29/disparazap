<?php

namespace App\Service;

use App\Models\User;

class UserService
{

    public function newUser($name, $email, $password)
    {
        try {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            return $user;
        } catch (\Exception $e) {
            dd("new user", $e);
        }
    }
}
