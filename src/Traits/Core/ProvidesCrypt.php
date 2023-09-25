<?php

namespace Larawatch\Traits\Core;


trait ProvidesCrypt
{
    protected function encryptPublic($plain_string)
    {
        openssl_public_encrypt($plain_string, $encrypted_string, base64_decode(config('larawatch.larawatch_public_key')));
        return base64_encode($encrypted_string);
    }

    protected function decryptPrivate($encrypted_string)
    {
        openssl_public_decrypt($encrypted_string, $plain_string, base64_decode(config('larawatch.larawatch_public_key')));
        return $plain_string;
    }

}
