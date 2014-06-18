<?php

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
     * @return mixed
     */
    public function getCode();

    /**
     * Returns the response body
     *
     * @return mixed
     */
    public function getRawBody();

    /**
     * Returns the decoded response body
     *
     * @return mixed
     */
    public function getResult();
}