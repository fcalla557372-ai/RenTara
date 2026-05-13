<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isCustomer()) {
            view()->share('userTheme', auth()->user()->theme ?? 'light');
        }
        return $next($request);
    }
}