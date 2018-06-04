<?php

namespace Kibo\Phast\HTTP;

use Kibo\Phast\HTTP\Exceptions\HTTPError;
use Kibo\Phast\HTTP\Exceptions\NetworkError;
use Kibo\Phast\ValueObjects\URL;

interface Client {

    /**
     * @param URL $url
     * @param array $headers
     * @return Response|false
     * @throws HTTPError
     * @throws NetworkError
     */
    public function get(URL $url, array $headers = []);

    /**
     * @param URL $url
     * @param array|string $data
     * @param array $headers
     * @return Response|false
     * @throws HTTPError
     * @throws NetworkError
     */
    public function post(URL $url, $data, array $headers = []);

}
