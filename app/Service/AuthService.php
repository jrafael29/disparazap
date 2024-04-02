<?php

namespace App\Service;

use App\Mail\User\Welcome;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use ServiceResponseTrait;
    public function login($email, $password, $remember = false): array
    {
        try {
            $credentials = [
                'email' => $email,
                'password' => $password
            ];
            if (Auth::attempt($credentials, $remember)) {
                return $this->successResponse(data: ['login' => true]);
            }
            return $this->errorResponse(message: 'Email e/ou senha inválidos', statusCode: 403);
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(message: 'Erro interno', statusCode: 500);
        }
    }

    public function register($name, $email, $password): array
    {
        try {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            Auth::login($user);

            // Mail::to($user)->send(new Welcome(data: [
            //     'toName' => $user->name,
            //     'toEmail' => $user->email,
            // ]));

            return $this->successResponse(data: ['user' => $user]);
        } catch (\Exception $e) {
            report($e);
            return $this->errorResponse(message: 'Erro interno', statusCode: 500);
        }
    }
}
