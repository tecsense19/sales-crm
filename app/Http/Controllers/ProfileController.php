<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'facebook' => 'nullable|string|url|max:255',
            'twitter' => 'nullable|string|url|max:255',
            'linkedin' => 'nullable|string|url|max:255',
            'instagram' => 'nullable|string|url|max:255',
        ]);

        // handle file upload if profile_photo is present
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo_path'] = $path;
        }

        // if name is sent as first_name and last_name (based on the view which split them)
        if ($request->has('first_name') || $request->has('last_name')) {
            $firstName = $request->input('first_name', explode(' ', $user->name)[0]);
            $lastName = $request->input('last_name', str_replace(explode(' ', $user->name)[0], '', $user->name));
            $validated['name'] = trim($firstName . ' ' . $lastName);
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
