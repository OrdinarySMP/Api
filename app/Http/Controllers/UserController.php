<?php

namespace App\Http\Controllers;

use App\Data\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request, User $user): User
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        Auth::guard('web')->login($user);

        return $user;
    }
}
