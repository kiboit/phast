<?php

namespace Kibo\Phast\Exceptions;

use Kibo\Phast\ValueObjects\URL;
use Throwable;

class ItemNotFoundException extends \Exception {
    /**
     * @var URL
     */
    private $url;

    public function __construct($message = '', $code = 0, Throwable $previous = null, URL $failed = null) {
        parent::__construct($message, $code, $previous);
        $this->url = $failed;
    }

    /**
     * @return URL
     */
    public function getUrl() {
        return $this->url;
    }
}
