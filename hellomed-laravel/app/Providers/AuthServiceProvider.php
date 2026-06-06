<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Article;
use App\Models\Doctor;
use App\Policies\AppointmentPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\DoctorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
        Article::class => ArticlePolicy::class,
        Doctor::class => DoctorPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
