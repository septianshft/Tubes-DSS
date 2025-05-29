<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->hasRole('teacher') && $student->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->hasRole('teacher') && $student->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('teacher') && $student->teacher_id === $user->id;
    }
}
