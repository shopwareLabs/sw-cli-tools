<?php

namespace ShopwareCli\Services\Rest\Curl;

class Response implements \ArrayAccess
{
    protected $rawBody;
    protected $body;
    protected $code;
    protected $errorMessage;
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
            $jsonErrors = array(
                JSON_ERROR_NONE => 'Es ist kein Fehler aufgetreten',
                JSON_ERROR_DEPTH => 'Die maximale Stacktiefe wurde erreicht',
                JSON_ERROR_CTRL_CHAR => 'Steuerzeichenfehler, möglicherweise fehlerhaft kodiert',
                JSON_ERROR_SYNTAX => 'Syntaxfehler',
            );

            $jsonError = $jsonErrors[json_last_error()];
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

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getRawBody()
    {
        return $this->rawBody;
    }

    public function getResult()
    {
        return $this->body;
    }

    public function debugDump()
    {
        if (!$this->success) {
            echo "<h2>Error</h2>\n";
            echo "Code:". $this->code . "<br />\n";
            echo "Message: <br />\n";
            echo $this->errorMessage;
        } else {
            echo "<h2>Success</h2>\n";
            echo "Message: <br />\n";
            echo print_r($this->body, true);
        }
    }

    public function offsetExists($offset)
    {
        $content = $this->getResult();
        return isset($content[$offset]);
    }

    public function offsetGet($offset)
    {
        $content = $this->getResult();
        return $content[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $content = $this->getResult();
        $content[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $content = $this->getResult();
        unset($content[$offset]);
    }
}