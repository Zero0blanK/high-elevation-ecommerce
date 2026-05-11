<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventCustomerAccessToAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('customer')->check() && !Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            abort(403, 'Customers are not authorized to access admin endpoints.');
        }

        return $next($request);
    }
}

