<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

interface HttpClient {

    public function retrieve(URL $url, array $headers = []);

}