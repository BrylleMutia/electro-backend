<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class isSellerMiddleware
{
    /**
     * Determine if user is seller
     * if not, abort request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->check() || !auth()->user()->role_id == Role::IS_SELLER) {
            abort(403, "User unauthorized");
        }

        return $next($request);
    }
}
