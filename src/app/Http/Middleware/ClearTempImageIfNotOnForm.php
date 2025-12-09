<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClearTempImageIfNotOnForm
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = optional($request->route())->getName();

        // 出品画像の allowed
        $allowedItemRoutes = [
            'sell.sell',
            'exhibit.exhibit',
        ];

        // プロフィール画像の allowed
        $allowedProfileRoutes = [
            'profile.edit',
            'save.profile',
        ];

        // allowed以外のルートの場合、出品画像セッションを消去
        if (!in_array($routeName, $allowedItemRoutes)) {
            Session::forget('temp_item_image_path');
        }

        // allowed以外のルートの場合、プロフィール画像セッションを消去
        if (!in_array($routeName, $allowedProfileRoutes)) {
            Session::forget('temp_profile_image_path');
        }

        return $next($request);
    }

}
