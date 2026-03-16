<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboard
    ) {}

    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.companies.index');
        }

        $companyId = $user->company_id;

        $stats = $this->dashboard->getStats($companyId);
        $chart = $this->dashboard->getWeeklyChart($companyId);
        $recentSimulations = $this->dashboard->getRecentSimulations($companyId);
        $topModels = $this->dashboard->getTopModels($companyId);

        return view('dashboard.index', [
            'user' => $user,
            'stats' => $stats,
            'chart' => $chart,
            'recentSimulations' => $recentSimulations,
            'topModels' => $topModels,
        ]);
    }
}
