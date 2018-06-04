<?php

namespace Kibo\Phast\HTTP;

use Kibo\Phast\ValueObjects\URL;

interface HTTPClient {

    public function get(URL $url, array $headers = []);

    public function post(URL $url, $data, array $headers = []);

}
