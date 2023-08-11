<?php
namespace App\Model\Classes;

class CryptoManager
{
    public static function Encode($value)
    {
        //require_once APP_ROOT . '/config/key';        
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        
        $encryptedData = openssl_encrypt($value, 'AES-256-CBC', 'in_time_you_will_know' /*__SecretKey__*/, 0, $iv);
        
        $encodedData = base64_encode($iv . $encryptedData);
        
        return $encodedData;
    }

    public static function Decode($value)
    {
        //require_once APP_ROOT . '/config/key';
        $decodedData = base64_decode($value);

        $ivSize = openssl_cipher_iv_length('AES-256-CBC');

        $iv = substr($decodedData, 0, $ivSize);

        $encryptedData = substr($decodedData, $ivSize);
                
        $decryptedData = openssl_decrypt($encryptedData, 'AES-256-CBC', 'in_time_you_will_know', 0, $iv);
        
        return $decryptedData;
    }
}
?>