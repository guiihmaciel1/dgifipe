<?php

namespace App\Http\Controllers;

class CompareController extends Controller
{
    public function index()
    {
        $specs = config('iphone_specs');
        $models = array_keys($specs);

        return view('compare.index', compact('specs', 'models'));
    }
}
