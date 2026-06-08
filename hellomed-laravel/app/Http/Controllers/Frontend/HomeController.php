<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home', [
            'departments' => Department::query()->where('is_active', true)->orderByDesc('is_featured')->orderByRaw('featured_order = 0')->orderBy('featured_order')->latest()->take(6)->get(),
            'doctors' => Doctor::query()->where('is_active', true)->orderByDesc('is_featured')->orderByRaw('featured_order = 0')->orderBy('featured_order')->latest()->take(8)->get(),
            'articles' => Article::query()->where('is_published', true)->with(['category', 'author.doctorProfile'])->orderByDesc('is_featured')->orderByRaw('featured_order = 0')->orderBy('featured_order')->latest('published_at')->take(3)->get(),
            'patientCount' => User::query()->where('role', 'patient')->count(),
            'totalDepartments' => Department::query()->where('is_active', true)->count(),
            'totalDoctors' => Doctor::query()->where('is_active', true)->count(),
        ]);
    }

    public function about()
    {
        return view('public.about');
    }
}
