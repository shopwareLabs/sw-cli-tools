<?php

namespace ShopwareCli\Services\Rest\Curl;

use ShopwareCli\Services\Rest\ResponseInterface;

/**
 * Response object wrapping a response body of a curl request.
 * Does provide a simple interface to access error messages and status codes
 * as well as the decoded result object
 *
 * Class Response
 * @package ShopwareCli\Services\Rest\Curl
 */
class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $rawBody;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var null|string
     */
    protected $errorMessage = null;

    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @param $body
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

        if (null === $decodedResult = json_decode($this->rawBody, true)) {
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
