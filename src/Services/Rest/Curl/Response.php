<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest\Curl;

use ShopwareCli\Services\Rest\ResponseInterface;

/**
 * Response object wrapping a response body of a curl request.
 * Does provide a simple interface to access error messages and status codes
 * as well as the decoded result object
 */
class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $rawBody;

    protected $body;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string|null
     */
    protected $errorMessage;

    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @param resource $curlHandle
     */
    public function __construct($body, $curlHandle)
    {
        $this->rawBody = $body;

        if ($body === false) {
            $this->errorMessage = curl_error($curlHandle);

            return;
        }

        $this->code = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        $decodedResult = json_decode($this->rawBody, true);
        if ($decodedResult === null) {
            $jsonError = json_last_error_msg();
            $rawErrorBody = print_r($body, true);

            $this->errorMessage = <<<error
<h2>Could not decode json</h2>
json_last_error: $jsonError;
<br>Raw:<br>
<pre>$rawErrorBody"</pre>";
error;

            return;
        }

        $this->body = $decodedResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->body;
    }
}
