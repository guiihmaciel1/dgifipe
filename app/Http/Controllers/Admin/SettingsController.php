<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        $settings = $company->getSettingsOrDefault();

        return view('admin.settings.edit', compact('company', 'settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'default_margin' => ['required', 'numeric', 'min:0', 'max:50'],
            'no_box' => ['required', 'numeric', 'min:0', 'max:30'],
            'no_cable' => ['required', 'numeric', 'min:0', 'max:30'],
            'screen_replaced' => ['required', 'numeric', 'min:0', 'max:30'],
            'face_id_issue' => ['required', 'numeric', 'min:0', 'max:30'],
        ]);

        $company = auth()->user()->company;

        CompanySetting::updateOrCreate(
            ['company_id' => $company->id],
            [
                'default_margin' => $data['default_margin'],
                'condition_discounts' => [
                    'no_box' => (float) $data['no_box'],
                    'no_cable' => (float) $data['no_cable'],
                    'screen_replaced' => (float) $data['screen_replaced'],
                    'face_id_issue' => (float) $data['face_id_issue'],
                ],
                'depreciation_rules' => config('dgifipe.default_depreciation_rules'),
            ]
        );

        ActivityLog::record('settings_updated', 'Configurações da empresa atualizadas');

        return back()->with('success', 'Configurações salvas com sucesso.');
    }
}
