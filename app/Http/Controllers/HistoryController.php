<?php

namespace App\Http\Controllers;

use App\Models\EvaluationSession;

class HistoryController extends Controller
{
    public function index()
    {
        $sessions = EvaluationSession::with(['simulations', 'user'])
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(20);

        return view('history.index', compact('sessions'));
    }

    public function show(EvaluationSession $session)
    {
        $session->load(['simulations', 'user']);

        return view('history.show', compact('session'));
    }
}
