<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'name' => 'DG Store',
            'slug' => 'dg-store',
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
            'name' => 'Admin DG',
            'email' => 'admin@dgstore.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'Vendedor Teste',
            'email' => 'seller@dgstore.com',
            'password' => 'password',
            'role' => 'seller',
            'is_active' => true,
        ]);
    }
}
