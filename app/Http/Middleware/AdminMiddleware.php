<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware       // kullanıcı admin mi değil mi kontrol eder, değilse hata verir
{
    public function handle(Request $request, Closure $next)
    {

        $user = auth('sanctum')->user();    // kullanıcıyı bu şekilde middleware ile alabiliriz
        if ($user) {
            Log::info($user);
            if ($user->is_admin) {
                return $next($request); // Admin ise istenen işleme devam et
            }
        }

        // Kullanıcı admin değilse ya da oturum açmamışsa 403 Forbidden yanıtı döndür
        return response()->json(['message' => 'Erişim izniniz yok.'], 403);
    }
}
