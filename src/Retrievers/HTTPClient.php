<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

interface HTTPClient {

    public function retrieve(URL $url, array $headers = []);

}