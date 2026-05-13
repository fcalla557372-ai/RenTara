<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        if ($user->status === 'inactive') {
            auth()->logout();
            return redirect('/login')->with('error', 'Your account has been deactivated.');
        }

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized action.');
            }
            return redirect('/dashboard')->with('error', 'Unauthorized action.');
        }

        return $next($request);
    }
}