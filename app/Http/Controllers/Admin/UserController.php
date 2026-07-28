<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|in:admin,gestionnaire,agent,client',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|in:admin,gestionnaire,agent,client',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('password')]);
        return back()->with('success', 'Mot de passe réinitialisé pour ' . $user->email);
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $statut = $user->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Compte de {$user->name} {$statut} avec succès.");
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent supprimer des comptes.');
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        \App\Models\Message::where('expediteur_id', $user->id)->delete();
        \App\Models\Notification::where('utilisateur_id', $user->id)->delete();
        \App\Models\HistoriqueStatut::where('utilisateur_id', $user->id)->delete();
        $user->auditLogs()->delete();

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
