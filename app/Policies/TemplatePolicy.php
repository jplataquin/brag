<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

class TemplatePolicy
{
    /**
     * Determine whether the user can update the template.
     */
    public function update(User $user, Template $template): bool
    {
        return $user->id === $template->user_id;
    }

    /**
     * Determine whether the user can delete the template.
     */
    public function delete(User $user, Template $template): bool
    {
        return $user->id === $template->user_id;
    }
}
