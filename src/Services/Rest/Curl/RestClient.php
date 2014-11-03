<?php

namespace ShopwareCli\Services\Rest\Curl;

use ShopwareCli\Services\Rest\RestInterface;

/**
 * RestClient based on CURL
 *
 * Class RestClient
 * @package ShopwareCli\Services\Rest\Curl
 */
class RestClient implements RestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

    const USER_AGENT = 'sw-cli-tools/1.0';

    /**
     * @var array
     */
    protected $validMethods = array(
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE
    );

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var resource
     */
    protected $cURL;

    /**
     * @param  string      $apiUrl
     * @param  string|null $username
     * @param  string|null $apiKey
     * @param  array       $curlOptions
     * @throws \Exception
     */
    public function __construct($apiUrl, $username = "", $apiKey = "", $curlOptions = array())
    {
        if (!filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid URL given');
        }

        $this->apiUrl = rtrim($apiUrl, '/') . '/';

        //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        if ($username != '' && $apiKey != '') {
            curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        }
        curl_setopt($this->cURL, CURLOPT_USERAGENT, self::USER_AGENT);

        curl_setopt_array($this->cURL, $curlOptions);
    }

    /**
     * Generic call method to perform an HTTP request with the given $method
     *
     * @param  string     $url
     * @param  string     $method
     * @param  array      $parameters
     * @param  array      $headers
     * @return Response
     * @throws \Exception
     */
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

        $headers = array_merge(
            array(
                'Content-Type: application/json; charset=utf-8',
            ),
            $headers
        );

        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $headers);
        $body = curl_exec($this->cURL);

        return new Response($body, $this->cURL);
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_GET, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_POST, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_PUT, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $parameters = array(), $headers = array())
    {
        return $this->call($url, self::METHOD_DELETE, $parameters, $headers);
    }
}
