<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected function authorizeAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = User::query();

        // Search by Name, Email, Phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Filter by Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,employee',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($plainPassword);

        $user = User::create($validated);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserCredentialsMail($user, $plainPassword));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome credentials email: ' . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'User created successfully and credentials email sent.');
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();

        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,employee',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Prevent admin from changing their own role to something else to avoid lockout
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot change your own Administrator role to avoid locking yourself out.');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // Safely detach relations
        $user->clients()->update(['assigned_to' => null]);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
