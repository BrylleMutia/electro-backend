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
        if(!auth()->guard("seller")->check() || !auth()->guard("seller")->user()) {
            return response()->json(['message' => "User unauthorized"], 403);
        }

        return $next($request);
    }
}
