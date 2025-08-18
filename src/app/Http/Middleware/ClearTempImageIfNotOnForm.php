<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClearTempImageIfNotOnForm
{
    public function handle(Request $request, Closure $next)
    {
        $allowedRoutes = [
            'exhibit.exhibit',
            'save.profile',
            'profile.edit',
            'sell.sell',
            'mypage',
            'index'
        ];

        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        if (!$routeName || !in_array($routeName, $allowedRoutes)) {
            Session::forget('temp_item_image_path');
            Session::forget('temp_profile_image_path');

        }

        return $next($request);
    }
}
