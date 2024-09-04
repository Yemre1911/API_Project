<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{
    public function AlterUsers(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'action' => 'required|string'   //make_admin , delete, remove_admin, add_ability, delete_ability, delete_token
        ]);

        $id = $validatedData['user_id'];
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Kullanıcı bulunamadı.'
            ], 404);
        }

        Log::info($user);
        $aciton = $validatedData['action'];
        switch ($aciton) {
            case 'make_admin':
                $user->is_admin = 1;
                $user->save();
                return response()->json([
                    'message' => 'Kullanıcı admin yapıldı.',
                    'user' => $user
                ], 200);

            case 'delete':
                $user->delete();
                return response()->json([
                    'message' => 'Kullanıcı başarıyla silindi.'
                ], 200);

            case 'remove_admin':
                $user->is_admin = 0;
                $user->save();
                return response()->json([
                    'message' => 'Kullanıcı artık admin değil.',
                    'user' => $user
                ], 200);
            case 'add_ability':
                // 'abilities' parametresini kontrol et
                $abilities = $request->input('abilities');

                if (!$abilities || !is_array($abilities)) {
                    return response()->json([
                        'message' => 'Geçersiz abilities parametresi. abilities bir dizi olmalıdır.'
                    ], 400);
                }

                // Kullanıcının mevcut token'ını al
                $token = $user->tokens()->latest()->first();

                if (!$token) {
                    return response()->json([
                        'message' => 'Kullanıcının aktif bir tokenı yok.'
                    ], 400);
                }

                // Mevcut yetenekleri birleştir ve tokenı güncelle
                $newAbilities = array_merge($token->abilities, $abilities);
                $token->abilities = $newAbilities;
                $token->save();

                return response()->json([
                    'message' => 'Kullanıcı yetenekleri başarıyla eklendi.',
                    'user' => $user,
                    'abilities' => $newAbilities,
                ], 200);

            case 'delete_abilities':
                // Kullanıcının mevcut token'ını al
                $token = $user->tokens()->latest()->first();

                if (!$token) {
                    return response()->json([
                        'message' => 'Kullanıcının aktif bir tokenı yok.'
                    ], 400);
                }

                // Tüm yetenekleri sil ve sadece "view" yeteneğini bırak
                $token->abilities = ['view'];
                $token->save();

                return response()->json([
                    'message' => 'Kullanıcının tüm yetenekleri silindi ve sadece "view" yeteneği kaldı.',
                    'user' => $user,
                    'abilities' => $token->abilities,
                ], 200);
            case 'delete_token':
                // Kullanıcının tüm tokenlarını sil
                $user->tokens()->delete();

                return response()->json([
                    'message' => 'Kullanıcının tüm tokenları silindi.',
                ], 200);

            default:
                return response()->json([
                    'message' => 'Geçersiz eylem.',
                    'message' => 'Yapılabilecek Eylemler:',
                    'make_admin: Kullanıcıyı Admin Yapar',
                    'delete: Kullanıcıyı siler',
                    'remove_admin: Birinin adminliğini iptal eder',
                    'add_ability: Birinin Tokenine Yetenek Verir',
                    'delete_abilities: Kullanıcının tüm yeteneklerini siler ve sadece "view" bırakır',
                    'delete_tokens: birinin tüm tokenleri siler (oturum kapatır)',
                ], 400);
        }
    }

    public function UserList($id)
    {
        Log::info($id);
        if (User::find($id))
            return User::find($id);
        else
            return response()->json([
                "message:" => 'Bu ID ye sahip bir kullanıcı yok'
            ]);
    }
}
