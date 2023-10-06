<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updatePassword(Request $request)
    {
        $user = User::findOrFail($request['id']);
        $this->validate($request, [
            'old_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!\Hash::check($value, $user->password)) {
                    return $fail(__('Current password is incorrect.'));
                }
            }],
            'new_password' => 'min:6|required',
            'confirm_password' => 'min:6|required_with:new_password|same:new_password'
        ]);

        $user->update(['password' => Hash::make($request['new_password'])]);
        return ['message' => 'updatng data'];
    }
}
