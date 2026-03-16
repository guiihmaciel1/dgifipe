<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationRequest;
use App\Jobs\SaveEvaluation;
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

        $accessoryChecks = [
            'no_box' => !empty($data['no_box']),
            'no_cable' => !empty($data['no_cable']),
        ];

        $result = $this->evaluator->evaluate(
            model: $data['model'],
            storage: $data['storage'],
            batteryHealth: (int) $data['battery_health'],
            deviceState: $data['device_state'],
            accessoryChecks: $accessoryChecks,
        );

        SaveEvaluation::dispatchAfterResponse(
            userId: auth()->id(),
            data: $data,
            result: $result,
        );

        return response()->json($result);
    }
}
