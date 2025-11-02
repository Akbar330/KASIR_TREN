<?php
// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $userRole = strtolower(auth()->user()->role->name);

        if (!in_array($userRole, array_map('strtolower', $roles))) {
            abort(403, 'Unauthorized action.');
        }


        return $next($request);
    }
}
