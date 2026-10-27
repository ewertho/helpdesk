<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::orderBy('name')->paginate(30)]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::enum(Role::class)]]);

        // Sem isso, o último admin poderia se rebaixar e trancar o painel para todo mundo.
        if ($user->is($request->user())) {
            return back()->withErrors(['role' => 'Você não pode alterar o próprio papel.']);
        }

        $user->role = Role::from($data['role']);
        $user->save();

        return back()->with('status', "{$user->name} agora é {$user->role->label()}.");
    }
}
