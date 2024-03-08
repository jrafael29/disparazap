<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Base64ToFile
{
    static public function storeImageFromBase64($base64, $filename)
    {
        try {
            if (empty($base64)) return false;
            // $filename = 'qrcodes/qr_' . uniqid() . '.png';
            $base64String = $base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String)) {
                $data = substr($base64String, strpos($base64String, ',') + 1);
                $data = base64_decode($data);
                Storage::put('public/' . $filename, $data);
                return $filename;
            }
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
