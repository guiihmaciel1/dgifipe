<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationRequest;
use App\Models\ActivityLog;
use App\Models\EvaluationSession;
use App\Services\EvaluatorService;
use Illuminate\Http\JsonResponse;

class EvaluatorController extends Controller
{
    public function __construct(
        private EvaluatorService $evaluator
    ) {}

    public function index()
    {
        $models = config('dgifipe.models');

        return view('evaluator.index', compact('models'));
    }

    public function calculate(EvaluationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->evaluator->evaluate(
            model: $data['model'],
            storage: $data['storage'],
            batteryHealth: (int) $data['battery_health'],
            conditions: $data['conditions'] ?? [],
        );

        $session = EvaluationSession::create([
            'user_id' => auth()->id(),
        ]);

        $session->simulations()->create([
            'model' => $data['model'],
            'storage' => $data['storage'],
            'battery_health' => $data['battery_health'],
            'conditions' => $data['conditions'] ?? [],
            'market_average' => $result['market_average'],
            'price_min' => $result['price_min'],
            'price_max' => $result['price_max'],
            'suggested_price' => $result['suggested_price'],
            'listings_count' => $result['listings_count'],
            'low_data_warning' => $result['low_data_warning'],
        ]);

        ActivityLog::record('evaluation', "{$data['model']} {$data['storage']}");

        return response()->json($result);
    }
}
