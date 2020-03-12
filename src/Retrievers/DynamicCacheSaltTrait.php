<?php


namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

trait DynamicCacheSaltTrait {
    public function getCacheSalt(URL $url) {
        return md5($url->toString()) . '-' . floor(time() / 7200);
    }
}
