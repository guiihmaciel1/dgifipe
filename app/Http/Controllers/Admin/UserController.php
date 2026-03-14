<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'seller'])],
        ]);

        $data['company_id'] = auth()->user()->company_id;
        $user = User::create($data);

        ActivityLog::record('user_created', "Usuário {$user->name} criado");

        return redirect()->route('admin.team.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $team)
    {
        $this->authorizeCompanyUser($team);

        return view('admin.users.edit', ['user' => $team]);
    }

    public function update(Request $request, User $team)
    {
        $this->authorizeCompanyUser($team);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($team->id)],
            'role' => ['required', Rule::in(['admin', 'seller'])],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:8', 'confirmed']]);
            $data['password'] = $request->password;
        }

        $team->update($data);

        ActivityLog::record('user_updated', "Usuário {$team->name} atualizado");

        return redirect()->route('admin.team.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function toggle(User $user)
    {
        $this->authorizeCompanyUser($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode desativar sua própria conta.');
        }

        $user->update([
            'is_active' => !$user->is_active,
            'active_session_token' => null,
        ]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        ActivityLog::record('user_toggled', "Usuário {$user->name} {$status}");

        return back()->with('success', "Usuário {$status} com sucesso.");
    }

    public function destroy(User $team)
    {
        $this->authorizeCompanyUser($team);

        if ($team->id === auth()->id()) {
            return back()->with('error', 'Você não pode remover sua própria conta.');
        }

        $name = $team->name;
        $team->delete();

        ActivityLog::record('user_deleted', "Usuário {$name} removido");

        return redirect()->route('admin.team.index')
            ->with('success', 'Usuário removido com sucesso.');
    }

    private function authorizeCompanyUser(User $user): void
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
