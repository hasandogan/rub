<?php

namespace App\Service\Auth;

use Symfony\Component\Uid\Uuid;

class EncryptionService
{
    const PRIVATE_KEY = '6d4fd0dc-adf7-4d3b-a5d4-7fb3732f7b2e';

    /**
     * @param $string
     * @return false|string
     */
    public function encrypt($string) {
        return openssl_encrypt($string,"AES-128-ECB", self::PRIVATE_KEY);
    }

    /**
     * @param $encryptedString
     * @return false|string
     */
    public function decrypt($encryptedString) {
        return openssl_decrypt($encryptedString,"AES-128-ECB", self::PRIVATE_KEY);
    }

    /**
     * @return false|string
     */
    public function createPublicKey()
    {
        return $this->encrypt(Uuid::v7());
    }

}