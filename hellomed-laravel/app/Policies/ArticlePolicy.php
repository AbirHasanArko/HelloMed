<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminOrStaff();
    }

    public function view(User $user, Article $article): bool
    {
        return $user->isAdminOrStaff() || $article->is_published;
    }

    public function create(User $user): bool
    {
        return $user->isAdminOrStaff() || $user->isDoctor();
    }

    public function update(User $user, Article $article): bool
    {
        return $user->isAdminOrStaff() || $article->user_id === $user->id;
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->isAdmin();
    }
}
