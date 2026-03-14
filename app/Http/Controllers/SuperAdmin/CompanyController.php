<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('users')->orderBy('name')->get();

        return view('superadmin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('superadmin.companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        $company = Company::create([
            'name' => $data['company_name'],
            'slug' => Str::slug($data['company_name']),
            'active' => true,
        ]);

        CompanySetting::create([
            'company_id' => $company->id,
            'default_margin' => 15.00,
            'depreciation_rules' => config('dgifipe.default_depreciation_rules'),
            'condition_discounts' => config('dgifipe.default_condition_discounts'),
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => $data['admin_password'],
            'role' => 'admin',
            'is_active' => true,
        ]);

        ActivityLog::record('company_created', "Empresa {$company->name} criada");

        return redirect()->route('superadmin.companies.index')
            ->with('success', "Empresa \"{$company->name}\" criada com sucesso.");
    }

    public function show(Company $company)
    {
        $company->load('users', 'settings');

        return view('superadmin.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('superadmin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        ActivityLog::record('company_updated', "Empresa {$company->name} atualizada");

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Empresa atualizada com sucesso.');
    }

    public function toggle(Company $company)
    {
        $company->update(['active' => !$company->active]);
        $status = $company->active ? 'ativada' : 'desativada';

        if (!$company->active) {
            $company->users()->update(['active_session_token' => null]);
        }

        ActivityLog::record('company_toggled', "Empresa {$company->name} {$status}");

        return back()->with('success', "Empresa {$status} com sucesso.");
    }

    public function createUser(Company $company)
    {
        return view('superadmin.companies.create-user', compact('company'));
    }

    public function storeUser(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'seller'])],
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'is_active' => true,
        ]);

        ActivityLog::record('user_created', "Usuário {$user->name} criado para {$company->name}");

        return redirect()->route('superadmin.companies.show', $company)
            ->with('success', "Usuário \"{$user->name}\" criado com sucesso.");
    }

    public function toggleUser(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active,
            'active_session_token' => null,
        ]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        ActivityLog::record('user_toggled', "Usuário {$user->name} {$status}");

        return back()->with('success', "Usuário {$status} com sucesso.");
    }
}
