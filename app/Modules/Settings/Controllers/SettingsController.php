<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Institution;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $institution = Institution::find(auth()->user()->current_institution_id);
        return view('settings::settings.index', compact('institution'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $institution = Institution::findOrFail(auth()->user()->current_institution_id);
        $institution->update($request->only('name', 'email', 'phone', 'address', 'city', 'country', 'currency', 'timezone'));

        return back()->with('success', 'Settings updated.');
    }
}
