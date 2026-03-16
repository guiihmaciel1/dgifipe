<?php

namespace App\Services;

use App\Models\EvaluationSession;
use App\Models\MarketListing;
use App\Models\OpportunityAlert;
use App\Models\Simulation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(int $companyId): array
    {
        $todayEvaluations = EvaluationSession::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->count();

        $totalEvaluations = Cache::remember(
            "dashboard:total_evaluations:{$companyId}",
            300,
            fn () => EvaluationSession::where('company_id', $companyId)->count()
        );

        $totalListings = Cache::remember(
            'dashboard:total_listings',
            300,
            fn () => MarketListing::excludeSealed()
                ->where('collected_at', '>=', now()->subDays(7))
                ->count()
        );

        $newOpportunities = OpportunityAlert::unread()->count();

        return compact('todayEvaluations', 'totalEvaluations', 'totalListings', 'newOpportunities');
    }

    public function getWeeklyChart(int $companyId): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));

        $counts = EvaluationSession::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];

        foreach ($days as $day) {
            $labels[] = Carbon::parse($day)->translatedFormat('D');
            $data[] = $counts[$day] ?? 0;
        }

        return compact('labels', 'data');
    }

    public function getRecentSimulations(int $companyId, int $limit = 5)
    {
        return Simulation::with('evaluationSession.user')
            ->whereHas('evaluationSession', fn ($q) => $q->where('company_id', $companyId))
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getTopModels(int $companyId, int $limit = 3): array
    {
        return Simulation::whereHas(
            'evaluationSession',
            fn ($q) => $q->where('company_id', $companyId)
        )
            ->select('model', DB::raw('COUNT(*) as total'))
            ->groupBy('model')
            ->orderByDesc('total')
            ->take($limit)
            ->pluck('total', 'model')
            ->toArray();
    }
}
