<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class CheckToken        // kullanıcının geçerli bir tokeni var mı yok mu kontrol eder
{
    public function handle(Request $request, Closure $next): Response
    {

        //Log::info("MİDDLEWARE ÇALIŞTI");

        $authHeader = $request->header('Authorization');       // Authorization başlığını al

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {

            return response()->json(['message' => 'Token bulunamadı veya geçersiz format'], Response::HTTP_UNAUTHORIZED);
        }

        // Bearer token'ı ayıkla
        $token = substr($authHeader, 7);

        // Token veritabanında var mı kontrol et
        $tokenRecord = PersonalAccessToken::findToken($token);

        if (!$tokenRecord) {
            return response()->json(['message' => 'Geçersiz token'], Response::HTTP_UNAUTHORIZED);
        }
        // Token süresinin dolup dolmadığını kontrol et
        if ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast()) {
            $tokenRecord->delete();
            return response()->json(['message' => 'Token süresi dolmuş.'], Response::HTTP_UNAUTHORIZED);
        }
        // Token geçerliyse işlemi devam ettir
        return $next($request);
    }
}
