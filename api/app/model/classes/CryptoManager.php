<?php
namespace App\Model\Classes;
use Key;

class CryptoManager
{
    public static function Encode($value)
    {
        // Generate a random Initialization Vector (IV) for encryption
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));

        // Encrypt the data before storing it in the cookie
        $encryptedData = openssl_encrypt($value, 'AES-256-CBC', __SecretKey__, 0, $iv);

        // Combine IV and encrypted data, then set the cookie
        $encodedData = base64_encode($iv . $encryptedData);
        
        return $encodedData;
    }

    public static function Decode($value)
    {
        $decodedData = base64_decode($value);
        $ivSize = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($decodedData, 0, $ivSize);
        $encryptedData = substr($decodedData, $ivSize);
        
        // Decrypt the data using openssl_decrypt
        $decryptedData = openssl_decrypt($encryptedData, 'AES-256-CBC', __SecretKey__, 0, $iv);
        
        return $decryptedData;
    }
}
?>