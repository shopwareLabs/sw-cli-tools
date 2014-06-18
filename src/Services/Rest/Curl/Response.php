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
class Response implements  ResponseInterface
{
    protected $rawBody;
    protected $body;
    protected $code;
    protected $errorMessage = null;
    protected $success = false;

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
     * If an error occurred during the curl request or json_decode, it will be available here.
     * Else null is returned
     *
     * @return null|string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Returns the http response code
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns the json_encoded response body
     *
     * @return mixed
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * Returns the decoded response body
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->body;
    }
}
