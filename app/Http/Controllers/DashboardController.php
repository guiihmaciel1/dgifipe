<?php

namespace App\Http\Controllers;

use App\Models\EvaluationSession;
use App\Models\MarketListing;
use App\Models\Simulation;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.companies.index');
        }

        $companyId = $user->company_id;

        $todayEvaluations = EvaluationSession::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->count();

        $totalEvaluations = EvaluationSession::where('company_id', $companyId)->count();

        $recentSimulations = Simulation::with('evaluationSession.user')
            ->whereHas('evaluationSession', fn ($q) => $q->where('company_id', $companyId))
            ->latest()
            ->take(5)
            ->get();

        $totalListings = Cache::remember('dashboard:total_listings', 300, fn () =>
            MarketListing::excludeSealed()
                ->where('collected_at', '>=', now()->subDays(7))->count()
        );

        return view('dashboard.index', compact(
            'todayEvaluations',
            'totalEvaluations',
            'recentSimulations',
            'totalListings',
        ));
    }
}
