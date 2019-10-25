<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest;

interface ResponseInterface
{
    /**
     * If an error occurred during request, it should be available here.
     * Else null should be returned
     *
     * @return null|string
     */
    public function getErrorMessage();

    /**
     * Returns the http response code
     *
     * @return int
     */
    public function getCode();

    /**
     * Returns the response body
     *
     * @return string
     */
    public function getRawBody();

    /**
     * Returns the decoded response body
     *
     * @return mixed
     */
    public function getResult();
}
