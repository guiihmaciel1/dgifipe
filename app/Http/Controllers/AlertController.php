<?php

namespace App\Http\Controllers;

use App\Models\OpportunityAlert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = OpportunityAlert::notDismissed()
            ->latest()
            ->paginate(20);

        OpportunityAlert::unread()->update(['status' => 'viewed']);

        return view('alerts.index', compact('alerts'));
    }

    public function dismiss(OpportunityAlert $alert)
    {
        $alert->update(['status' => 'dismissed']);

        return back()->with('success', 'Alerta dispensado.');
    }

    public function count()
    {
        return response()->json([
            'count' => OpportunityAlert::unread()->count(),
        ]);
    }
}
