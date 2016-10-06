<?php

namespace ShopwareCli\Services\Rest;

/**
 * General interfaces for REST classes
 *
 * Class RestInterface
 */
interface RestInterface
{
    /**
     * Perform a HTTP GET request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     *
     * @return ResponseInterface
     */
    public function get($url, $parameters = [], $headers = []);

    /**
     * Perform a HTTP POST request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     *
     * @return ResponseInterface
     */
    public function post($url, $parameters = [], $headers = []);

    /**
     * Perform a HTTP PUT request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     *
     * @return ResponseInterface
     */
    public function put($url, $parameters = [], $headers = []);

    /**
     * Perform a HTTP DELETE request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     *
     * @return ResponseInterface
     */
    public function delete($url, $parameters = [], $headers = []);
}
