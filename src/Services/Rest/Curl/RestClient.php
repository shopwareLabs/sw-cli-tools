<?php

namespace ShopwareCli\Services\Rest\Curl;

use ShopwareCli\Services\Rest\RestInterface;

class RestClient implements RestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

    protected $validMethods = array(
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE
    );

    protected $apiUrl;
    protected $cURL;

    public function __construct($apiUrl, $username, $apiKey, $curlOptions = array())
    {
        if (!filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid URL given');
        }

        $this->apiUrl = rtrim($apiUrl, '/') . '/';

        //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        curl_setopt(
            $this->cURL,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json; charset=utf-8',
            )
        );

        curl_setopt_array($this->cURL, $curlOptions);
    }

    public function call($url, $method = self::METHOD_GET, $parameters = array(), $headers = array())
    {
        if (!in_array($method, $this->validMethods)) {
            throw new \Exception('Invalid HTTP method: ' . $method);
        }
        $url = $this->apiUrl . $url;

        $dataString = json_encode($parameters);
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);

        $body = curl_exec($this->cURL);

        return new Response($body, $this->cURL);
    }

    public function get($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_GET, $parameters, $headers);
    }

    public function post($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_POST, $parameters, $headers);
    }

    public function put($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_PUT, $parameters, $headers);
    }

    public function delete($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_DELETE, $parameters, $headers);
    }
}