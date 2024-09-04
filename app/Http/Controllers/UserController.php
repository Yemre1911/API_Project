<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;





class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [

            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',

        ]);

        if ($validatedData->fails()) {
            // Hataları döndür
            return response()->json([
                'errors' => $validatedData->errors()
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')), // Şifreyi hash'leyin
        ]);

        $token = $user->createToken('Token Name', ['view'])->plainTextToken;

        $user->tokens()->latest()->first()->update([
            'expires_at' => now()->addMinutes(15),
        ]);

        return response()->json([
            'message' => 'Kullanıcı başarıyla oluşturuldu.',
            'user' => $user,
            'token' => $token

        ], 201);
    }



    public function login(Request $request)
    {
        Log::info("GİRİLDİ LOGİNE");
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt([
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ])) {
            // Get the authenticated user
            $user = Auth::user();

            // Get all tokens for the user
            $tokens = $user->tokens;

            // Eğer mevcut token varsa, yeni token oluşturma
            foreach ($tokens as $token) {
                if ($token->expires_at && $token->expires_at->isPast()) {
                    // Token varsa ama zamanı dolmuşsa, bu token'ı sil
                    $token->delete();
                } else {
                    return response()->json([
                        'message' => 'Kullanıcı oturumu zaten açık.',
                        'user' => $user,
                    ], 200);
                }
            }

            // Yeni token oluşturma
            if ($user->is_admin) {
                $token = $user->createToken('Admin Token', ['*'])->plainTextToken;
            } else {
                $token = $user->createToken('Normal Token', ['view'])->plainTextToken;
            }

            $user->tokens()->latest()->first()->update([
                'expires_at' => now()->addMinutes(15),
            ]);

            return response()->json([
                'message' => 'Başarıyla giriş yapıldı.',
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'message' => 'Giriş bilgileri geçersiz.'
            ], 401);
        }
    }



    public function logout(Request $request)
    {

        // $user = Auth::user();                  // Kullanıcıyı doğrulama
        $user = auth('sanctum')->user();        // kullanıcıyı bu şekilde middleware ile alabiliriz

        if ($user) {
            // Başarı mesajı döndür
            return response()->json(['message' => 'Başarıyla çıkış yapıldı.'], 200);
        }

        // Kullanıcı bulunamazsa hata döndür
        return response()->json(['message' => 'Geçerli bir kullanıcı bulunamadı.'], 401);
    }
}
