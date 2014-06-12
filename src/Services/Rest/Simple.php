<?php

namespace ShopwareCli\Services\Rest;

/**
 * Simple "REST" client which is capable of normal POST requests
 *
 * Class Simple
 * @package ShopwareCli\Services\Rest
 */
class Simple implements RestInterface
{
    public function get($url, $parameters=array(), $headers=array())
    {
        foreach ($headers as $key => &$value) {
            $value = "{$key}: {$value}";
        }

        $opts = array(
          'http'=>array(
            'method'=>"GET",
            'timeout' => 300,
            'header'=>implode("\r\n", $headers)
          )
        );

        $parameters = http_build_query($parameters, '', "&");

        $parameters = empty($parameters) ? '' : '?' . $parameters;

        $context = stream_context_create($opts);

        $result = file_get_contents($url . $parameters, false, $context);

        return $result;
    }

    public function post($url, $parameters = array(), $headers = array())
    {
        $data = http_build_query($parameters);

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );

        $context  = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);

        return $result;
    }
}
