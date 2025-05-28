<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        return User::create([
            'username' => $input['username'],
            'password' => Hash::make($input['password']),
        ]);
    }

    protected function passwordRules()
    {
        return ['required', 'string', 'min:8']; 
    }
}
