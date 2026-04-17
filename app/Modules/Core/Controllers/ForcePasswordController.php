<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Force password change — DPG Criterion 9.
 * Shown to users with must_change_password=true (temp passwords, password resets).
 */
class ForcePasswordController extends Controller
{
    public function show()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        if (empty(Auth::user()->must_change_password)) {
            return redirect('/dashboard');
        }

        return view('core::auth.force-password');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        if (Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['password' => 'New password must differ from the temporary one.']);
        }

        $user->password = Hash::make($data['password']);
        $user->must_change_password = false;
        $user->save();

        return redirect('/dashboard')->with('success', 'Password updated.');
    }
}
