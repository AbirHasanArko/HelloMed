<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AvailableTest;

class AvailableTestController extends Controller
{
    public function index()
    {
        $tests = AvailableTest::where('is_active', true)->orderBy('name')->get();
        return view('frontend.available_tests.index', compact('tests'));
    }

    public function show(AvailableTest $availableTest)
    {
        abort_if(!$availableTest->is_active, 404);
        return view('frontend.available_tests.show', compact('availableTest'));
    }
}
