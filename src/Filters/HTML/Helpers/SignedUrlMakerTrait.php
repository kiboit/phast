<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Security\ServiceSignature;

trait SignedUrlMakerTrait {

    /**
     * @param string $url
     * @param array $params
     * @param ServiceSignature $signature
     * @return string
     */
    protected function makeSignedUrl($url, $params, ServiceSignature $signature) {
        $query = http_build_query($params);
        $query .= '&token=' . $signature->sign($query);
        $glue = strpos($url, '?') === false ? '?' : '&';
        return $url . $glue . $query;
    }

}
