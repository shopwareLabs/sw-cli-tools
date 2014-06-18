<?php

namespace ShopwareCli\Services\Rest;

/**
 * General interfaces for REST classes
 *
 * Class RestInterface
 * @package ShopwareCli\Rest
 */
interface RestInterface
{
    /**
     * Perform a HTTP GET request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     * @return ResponseInterface
     */
    public function get($url, $parameters = array(), $headers = array());

    /**
     * Perform a HTTP POST request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     * @return ResponseInterface
     */
    public function post($url, $parameters = array(), $headers = array());

    /**
     * Perform a HTTP PUT request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     * @return ResponseInterface
     */
    public function put($url, $parameters = array(), $headers = array());

    /**
     * Perform a HTTP DELETE request
     *
     * @param  string            $url
     * @param  array             $parameters
     * @param  array             $headers
     * @return ResponseInterface
     */
    public function delete($url, $parameters = array(), $headers = array());
}
