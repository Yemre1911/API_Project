<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlterTableController extends Controller
{
    public function AlterTable(Request $request)
    {
        $validatedData = $request->validate([

            'model_name' => 'string|required',
            'id' => 'required|integer',
            'column' => 'required|string',
            'value' => 'required'
        ]);

        $table = $validatedData['model_name'];
        $id = $validatedData['id'];
        $column = $validatedData['column'];
        $value = $validatedData['value'];

        try {
            // Dinamik olarak model bulunması
            $modelClass = 'App\\Models\\' . ucfirst($table);
            if (!class_exists($modelClass)) {
                return response()->json([
                    'message' => 'Tablo bulunamadı.'
                ], 404);
            }

            // Veritabanında güncellenecek kaydı bulma
            $record = $modelClass::find($id);
            if (!$record) {
                return response()->json([
                    'message' => 'Kayıt bulunamadı.'
                ], 404);
            }

            // Sütun güncellemesi
            $record->$column = $value;
            $record->save();

            return response()->json([
                'message' => 'Kayıt başarıyla güncellendi.',
                'record' => $record
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
