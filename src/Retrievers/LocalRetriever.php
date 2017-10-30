<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\FileSystem\FileSystemAccessor;
use Kibo\Phast\ValueObjects\URL;

class LocalRetriever implements Retriever {

    /**
     * @var array
     */
    private $map;

    /**
     * @var FileSystemAccessor
     */
    private $fsAccessor;

    /**
     * LocalRetriever constructor.
     *
     * @param array $map
     * @param FileSystemAccessor $fsAccessor
     */
    public function __construct(array $map, FileSystemAccessor $fsAccessor = null) {
        $this->map = $map;
        if ($fsAccessor) {
            $this->fsAccessor = $fsAccessor;
        } else {
            $this->fsAccessor = new FileSystemAccessor();
        }
    }

    public function retrieve(URL $url) {
        if (!isset ($this->map[$url->getHost()])) {
            return false;
        }
        $base = URL::fromString($this->map[$url->getHost()]);
        $file = $this->getPathInBase($base, $url->getPath());
        if ($file === false) {
            return false;
        }
        return $this->fsAccessor->file_get_contents($file);
    }

    private function getPathInBase($base, $file) {
        $base = $this->fsAccessor->realpath($base);
        if (!$base) {
            return false;
        }
        $path = $this->fsAccessor->realpath($base . '/' . $file);
        if (!$path) {
            return false;
        }
        if (strpos($path, $base) !== 0) {
            return false;
        }
        return $path;
    }

}
