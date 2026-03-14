<?php

namespace App\Http\Controllers;

use App\Models\MarketListing;
use Illuminate\Http\Request;

class MarketRadarController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketListing::query()->recent();

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        if ($request->filled('storage')) {
            $query->where('storage', $request->storage);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $listings = $query->latest('collected_at')->paginate(30);
        $models = config('dgifipe.models');

        return view('market-radar.index', compact('listings', 'models'));
    }
}
