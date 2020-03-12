<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

interface Retriever {
    /**
     * @param URL $url
     * @return string|bool
     */
    public function retrieve(URL $url);

    /**
     * @param URL $url
     * @return integer|bool
     */
    public function getCacheSalt(URL $url);
}
