<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManualListingRequest;
use App\Models\ActivityLog;
use App\Models\MarketListing;

class ManualUploadController extends Controller
{
    public function index()
    {
        $models = config('dgifipe.models');
        $cities = config('dgifipe.cities');

        return view('manual-upload.index', compact('models', 'cities'));
    }

    public function store(ManualListingRequest $request)
    {
        $data = $request->validated();
        $screenshotPath = null;

        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')
                ->store('screenshots', 'local');
        }

        MarketListing::create([
            'model' => $data['model'],
            'storage' => $data['storage'],
            'price' => $data['price'],
            'city' => $data['city'],
            'source' => 'manual',
            'title' => $data['title'] ?? null,
            'screenshot_path' => $screenshotPath,
            'collected_at' => now()->toDateString(),
        ]);

        ActivityLog::record('manual_upload', "{$data['model']} {$data['storage']} - R$ {$data['price']}");

        return redirect()->route('manual-upload')
            ->with('success', 'Anúncio cadastrado com sucesso.');
    }
}
