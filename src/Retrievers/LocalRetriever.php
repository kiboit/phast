<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

class LocalRetriever implements Retriever {

    /**
     * @var array
     */
    private $map;

    /**
     * @var callable
     */
    private $retrieveCb;

    /**
     * LocalRetriever constructor.
     *
     * @param array $map
     * @param callable $retrieveCb
     */
    public function __construct(array $map, callable $retrieveCb = null) {
        $this->map = $map;
        if ($retrieveCb) {
            $this->retrieveCb = $retrieveCb;
        } else {
            $this->retrieveCb = function ($file) {
                return @file_get_contents($file);
            };
        }
    }

    public function retrieve(URL $url) {
        if (!isset ($this->map[$url->getHost()])) {
            return false;
        }
        $base = URL::fromString($this->map[$url->getHost()]);
        $path = rtrim($base, '/') . '/' . ltrim($url->getPath(), '/');
        return call_user_func($this->retrieveCb, $path);
    }

}
