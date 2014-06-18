<?php

namespace ShopwareCli\Services\Rest\Curl;

use ShopwareCli\Services\Rest\RestInterface;

class RestClient implements RestInterface
{
    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';

    protected $validMethods = array(
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE
    );

    protected $apiUrl;
    protected $cURL;

    public function __construct($apiUrl, $username, $apiKey)
    {
        if (!filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid URL given');
        }

        $this->apiUrl = rtrim($apiUrl, '/') . '/';

         //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
        ));
    }

    public function call($url, $method = self::METHOD_GET, $data = array(), $params = array())
    {
        if (!in_array($method, $this->validMethods)) {
            throw new \Exception('Invalid HTTP method: ' . $method);
        }
        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
        }
        $url = rtrim($url, '?') . '?';
        $url = $this->apiUrl . $url . $queryString;
        $dataString = json_encode($data);
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