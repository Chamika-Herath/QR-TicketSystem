<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): Response
    {
        // Prevent non-admins/staff from viewing users
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'staff') {
            abort(403, 'Unauthorized action.');
        }

        $query = User::with('creator')->orderBy('name');
        $manager = null;

        if (auth()->user()->role === 'admin') {
            if ($request->has('manager_id')) {
                $query->where('role', 'scanner')->where('created_by', $request->input('manager_id'));
                $manager = User::find($request->input('manager_id'));
            } else {
                $query->whereIn('role', ['admin', 'staff']);
            }
        } elseif (auth()->user()->role === 'staff') {
            $query->where('role', 'scanner')->where('created_by', auth()->id());
        }

        $users = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/UsersIndex', [
            'users' => $users,
            'manager' => $manager,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'staff') {
            abort(403, 'Unauthorized action.');
        }

        $allowedRoles = auth()->user()->role === 'admin' ? 'admin,staff,scanner' : 'scanner';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:' . $allowedRoles,
            'allowed_event_limit' => 'required|integer|min:0',
            'allowed_ticket_limit' => 'required|integer|min:0',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'allowed_event_limit' => auth()->user()->role === 'staff' ? 0 : $validated['allowed_event_limit'],
            'allowed_ticket_limit' => auth()->user()->role === 'staff' ? 0 : $validated['allowed_ticket_limit'],
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'User created successfully.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'staff') {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->role === 'staff' && $user->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $allowedRoles = auth()->user()->role === 'admin' ? 'admin,staff,scanner' : 'scanner';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:' . $allowedRoles,
            'allowed_event_limit' => 'required|integer|min:0',
            'allowed_ticket_limit' => 'required|integer|min:0',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'allowed_event_limit' => auth()->user()->role === 'staff' ? 0 : $validated['allowed_event_limit'],
            'allowed_ticket_limit' => auth()->user()->role === 'staff' ? 0 : $validated['allowed_ticket_limit'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'staff') {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->role === 'staff' && $user->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
