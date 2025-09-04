<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth('admin')->user();
        
        if (!$user || !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Insufficient permissions'], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}