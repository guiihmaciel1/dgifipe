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
            'resale_margin' => ['required', 'numeric', 'min:0', 'max:100'],
            'battery_excellent' => ['required', 'numeric', 'min:-50', 'max:50'],
            'battery_good' => ['required', 'numeric', 'min:-50', 'max:50'],
            'battery_regular' => ['required', 'numeric', 'min:-50', 'max:50'],
            'battery_bad' => ['required', 'numeric', 'min:-50', 'max:50'],
            'state_original' => ['required', 'numeric', 'min:-50', 'max:50'],
            'state_repaired' => ['required', 'numeric', 'min:-50', 'max:50'],
            'acc_complete' => ['required', 'numeric', 'min:-50', 'max:50'],
            'acc_partial' => ['required', 'numeric', 'min:-50', 'max:50'],
            'acc_none' => ['required', 'numeric', 'min:-50', 'max:50'],
        ]);

        $company = auth()->user()->company;

        CompanySetting::updateOrCreate(
            ['company_id' => $company->id],
            [
                'default_margin' => $data['default_margin'],
                'resale_margin' => $data['resale_margin'],
                'battery_rules' => [
                    ['min' => 90, 'max' => 100, 'modifier' => (float) $data['battery_excellent']],
                    ['min' => 80, 'max' => 89,  'modifier' => (float) $data['battery_good']],
                    ['min' => 70, 'max' => 79,  'modifier' => (float) $data['battery_regular']],
                    ['min' => 0,  'max' => 69,  'modifier' => (float) $data['battery_bad']],
                ],
                'device_state_options' => [
                    'original' => (float) $data['state_original'],
                    'repaired' => (float) $data['state_repaired'],
                ],
                'accessory_options' => [
                    'complete' => (float) $data['acc_complete'],
                    'partial'  => (float) $data['acc_partial'],
                    'none'     => (float) $data['acc_none'],
                ],
            ]
        );

        ActivityLog::record('settings_updated', 'Configurações do avaliador atualizadas');

        return back()->with('success', 'Configurações salvas com sucesso.');
    }
}
