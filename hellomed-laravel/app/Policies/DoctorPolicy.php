<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;

class DoctorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminOrStaff();
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $user->isAdminOrStaff();
    }
}
