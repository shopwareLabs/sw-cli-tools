<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services;

class OpenSSLVerifier
{
    private $publicKey;

    /**
     * @param string $publicKey
     */
    public function __construct($publicKey)
    {
        if (!is_readable($publicKey)) {
            throw new \InvalidArgumentException(sprintf(
                'Public keyfile (%s) not readable',
                $publicKey
            ));
        }

        $this->publicKey = $publicKey;
    }

    public function isSystemSupported(): bool
    {
        return \function_exists('openssl_verify');
    }

    /**
     * @param string $message
     * @param string $signature
     *
     * @throws \RuntimeException
     */
    public function isValid($message, $signature): bool
    {
        $publicKey = trim(file_get_contents($this->publicKey));

        if ($pubkeyid = openssl_pkey_get_public($publicKey) === false) {
            while ($errors[] = openssl_error_string());
            throw new \RuntimeException(sprintf("Error during public key read: \n%s", implode("\n", $errors)));
        }

        $signature = base64_decode($signature);

        // state whether signature is okay or not
        $ok = (int) openssl_verify($message, $signature, $pubkeyid);

        // free the key from memory
        openssl_free_key($pubkeyid);

        if ($ok === 1) {
            return true;
        }

        if ($ok === 0) {
            return false;
        }

        while ($errors[] = openssl_error_string());
        throw new \RuntimeException(sprintf("Error during private key read: \n%s", implode("\n", $errors)));
    }
}
