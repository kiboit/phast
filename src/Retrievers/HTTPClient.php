<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

interface HTTPClient {

    public function get(URL $url, array $headers = []);

}
