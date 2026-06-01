<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAdminOrStaff(): bool
    {
        return in_array($this->role, ['admin', 'staff'], true);
    }

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function medicineOrders(): HasMany
    {
        return $this->hasMany(MedicineOrder::class);
    }

    public function doctorProfile(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function doctorReviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function articleComments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function qnaQuestions(): HasMany
    {
        return $this->hasMany(QnaQuestion::class);
    }

    public function qnaAnswers(): HasMany
    {
        return $this->hasMany(QnaAnswer::class);
    }
}
