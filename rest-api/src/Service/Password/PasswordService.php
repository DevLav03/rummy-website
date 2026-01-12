<?php

namespace App\Service\Password;

/**
 * Service
 */
final class PasswordService {


    public function __construct()
    {
       
    }

    public function passwordEncrytion($password){
      
        $original_string = $password; 
        $cipher_algo = "AES-128-CTR"; 
        $iv_length = openssl_cipher_iv_length($cipher_algo);
        $option = 0; 
        $encrypt_iv = '8746376827619797'; 
        $encrypt_key = "Vinora@321"; 
        $encrypted_string = openssl_encrypt($original_string, $cipher_algo, $encrypt_key, $option, $encrypt_iv);

        return $encrypted_string;
       
    }

    public function passwordDecrypted($password){

        $encrypted_string = $password;
        $cipher_algo = "AES-128-CTR"; 
        $iv_length = openssl_cipher_iv_length($cipher_algo);
        $option = 0; 
        $decrypt_iv = '8746376827619797'; 
        $decrypt_key = "Vinora@321"; 
        $decrypted_string=openssl_decrypt ($encrypted_string, $cipher_algo,$decrypt_key, $option, $decrypt_iv);


    }

}