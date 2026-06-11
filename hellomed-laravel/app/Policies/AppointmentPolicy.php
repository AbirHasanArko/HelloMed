<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminOrStaff();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isAdminOrStaff() || $appointment->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isAdminOrStaff()) {
            return true;
        }

        return $appointment->user_id === $user->id
            && in_array($appointment->status, ['pending', 'confirmed'], true);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin();
    }
}
