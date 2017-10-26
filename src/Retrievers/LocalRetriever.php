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
            $this->retrieveCb = function ($file, $base) {
                $base = realpath($base) . '/';
                if (!$base) {
                    return false;
                }
                $path = realpath($base . $file);
                if (!$path) {
                    return false;
                }
                if (strpos($path, $base) !== 0) {
                    return false;
                }
                return @file_get_contents($path);
            };
        }
    }

    public function retrieve(URL $url) {
        if (!isset ($this->map[$url->getHost()])) {
            return false;
        }
        $base = URL::fromString($this->map[$url->getHost()]);
        return call_user_func($this->retrieveCb, $url->getPath(), $base);
    }

}
