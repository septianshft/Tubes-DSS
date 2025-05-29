<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the student from the route parameters
        $student = $request->route('student');

        // If no student parameter, continue
        if (!$student) {
            return $next($request);
        }

        // If student is not a Student model, try to find it
        if (!$student instanceof Student) {
            $student = Student::find($student);
        }

        // If student doesn't exist, continue (let the controller handle the 404)
        if (!$student) {
            return $next($request);
        }

        // Check if the authenticated user is the teacher of this student
        if (Auth::check() && Auth::user()->hasRole('teacher') && $student->teacher_id !== Auth::id()) {
            abort(403, 'You can only access your own students.');
        }

        return $next($request);
    }
}
