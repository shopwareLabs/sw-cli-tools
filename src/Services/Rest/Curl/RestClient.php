<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest\Curl;

use ShopwareCli\Services\Rest\RestInterface;

/**
 * RestClient based on CURL
 */
class RestClient implements RestInterface
{
    private const METHOD_GET = 'GET';
    private const METHOD_PUT = 'PUT';
    private const METHOD_POST = 'POST';
    private const METHOD_DELETE = 'DELETE';

    private const USER_AGENT = 'sw-cli-tools/1.0';

    /**
     * @var array
     */
    protected $validMethods = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
    ];

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var resource
     */
    protected $cURL;

    /**
     * @param string      $apiUrl
     * @param string|null $username
     * @param string|null $apiKey
     * @param array       $curlOptions
     *
     * @throws \Exception
     */
    public function __construct($apiUrl, $username = '', $apiKey = '', $curlOptions = [])
    {
        if (!\filter_var($apiUrl, \FILTER_VALIDATE_URL)) {
            throw new \RuntimeException('Invalid URL given');
        }

        $this->apiUrl = \rtrim($apiUrl, '/') . '/';

        // Initializes the cURL instance
        $this->cURL = \curl_init();
        \curl_setopt($this->cURL, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($this->cURL, \CURLOPT_FOLLOWLOCATION, false);
        if ($username !== '' && $apiKey !== '') {
            \curl_setopt($this->cURL, \CURLOPT_USERPWD, $username . ':' . $apiKey);
        }
        \curl_setopt($this->cURL, \CURLOPT_USERAGENT, self::USER_AGENT);

        \curl_setopt_array($this->cURL, $curlOptions);
    }

    /**
     * Generic call method to perform an HTTP request with the given $method
     *
     * @throws \Exception
     */
    public function call(string $url, string $method = self::METHOD_GET, array $parameters = [], array $headers = []): Response
    {
        if (!\in_array($method, $this->validMethods, true)) {
            throw new \RuntimeException('Invalid HTTP method: ' . $method);
        }
        $url = $this->apiUrl . $url;

        $dataString = \json_encode($parameters);
        \curl_setopt($this->cURL, \CURLOPT_URL, $url);
        \curl_setopt($this->cURL, \CURLOPT_CUSTOMREQUEST, $method);
        \curl_setopt($this->cURL, \CURLOPT_POSTFIELDS, $dataString);

        \array_unshift($headers, 'Content-Type: application/json; charset=utf-8');

        \curl_setopt($this->cURL, \CURLOPT_HTTPHEADER, $headers);
        $body = \curl_exec($this->cURL);

        return new Response($body, $this->cURL);
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, $parameters = [], $headers = [])
    {
        return $this->call($url, self::METHOD_GET, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $parameters = [], $headers = [])
    {
        return $this->call($url, self::METHOD_POST, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $parameters = [], $headers = [])
    {
        return $this->call($url, self::METHOD_PUT, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $parameters = [], $headers = [])
    {
        return $this->call($url, self::METHOD_DELETE, $parameters, $headers);
    }
}
