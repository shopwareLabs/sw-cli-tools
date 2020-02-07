<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest;

/**
 * General interfaces for REST classes
 */
interface RestInterface
{
    /**
     * Perform a HTTP GET request
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    public function get($url, $parameters = [], $headers = []);

    /**
     * Perform a HTTP POST request
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    public function post($url, $parameters = [], $headers = []);

    /**
     * Perform a HTTP PUT request
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    public function put($url, $parameters = [], $headers = []);

    /**
     * Perform a HTTP DELETE request
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    public function delete($url, $parameters = [], $headers = []);
}
