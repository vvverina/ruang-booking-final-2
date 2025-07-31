<?php
// namespace App\Http\Middleware;
// use Closure;
// class AdminMiddleware {
//     public function handle($request, Closure $next) {
//         if (auth()->check() && auth()->user()->role === 'admin') {
//             return $next($request);
//         }
//         abort(403, 'Unauthorized');
//     }
// }

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
