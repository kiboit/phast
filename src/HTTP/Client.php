<?php

namespace Kibo\Phast\HTTP;

use Kibo\Phast\ValueObjects\URL;

interface Client {
    /**
     * Retrieve a URL using the GET HTTP method
     *
     * @param URL $url
     * @param array $headers - headers to send in headerName => headerValue format
     * @return Response
     * @throws \Exception
     */
    public function get(URL $url, array $headers = []);

    /**
     * Send data to a URL using the POST HTTP method
     *
     * @param URL $url
     * @param array|string $data - if array, it will be encoded as form data, if string - will be sent as is
     * @param array $headers - headers to send in headerName => headerValue format
     * @return Response
     * @throws \Exception
     */
    public function post(URL $url, $data, array $headers = []);
}
