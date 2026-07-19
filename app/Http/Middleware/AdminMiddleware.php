<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('admin.login')->with('status', 'Please sign in to continue to the admin area.');
        }

        if (! $request->user()->isAdmin()) {
            abort(403, 'Area ini hanya untuk admin.');
        }

        $lastActivity = (int) $request->session()->get('admin_last_activity', 0);
        $hasToken = $request->session()->has('admin_session_token');
        $timeoutSeconds = max((int) config('session.admin_lifetime', 30), 1) * 60;

        if (! $hasToken || ! $lastActivity || (time() - $lastActivity) > $timeoutSeconds) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('status', 'Your admin session has expired. Please sign in again.');
        }

        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }
}
