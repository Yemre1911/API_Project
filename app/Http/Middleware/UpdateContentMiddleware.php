<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateContentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user();

        if ($user && $user->tokenCan('update')) {
            // Eğer kullanıcı 'update' yeteneğine sahipse işleme devam et
            return $next($request);
        }

        // Kullanıcı 'update' yeteneğine sahip değilse 403 Forbidden yanıtı döndür
        return response()->json(['message' => 'Bu işlemi gerçekleştirmek için yetkiniz yok. (Token Ability Problem)'], 403);
    }
}
