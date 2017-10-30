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
        $file = $this->getFileForURL($url);
        if ($file === false) {
            return false;
        }
        return $this->fsAccessor->file_get_contents($file);
    }

    public function getLastModificationTime(URL $url) {
        $file = $this->getFileForURL($url);
        if ($file === false) {
            return false;
        }
        return $this->fsAccessor->filemtime($file);
    }

    private function getFileForURL(URL $url) {
        if (!isset ($this->map[$url->getHost()])) {
            return false;
        }
        $base = URL::fromString($this->map[$url->getHost()]);
        $base = $this->fsAccessor->realpath($base);
        if (!$base) {
            return false;
        }
        $path = $this->fsAccessor->realpath($base . '/' . $url->getPath());
        if (!$path) {
            return false;
        }
        if (strpos($path, $base) !== 0) {
            return false;
        }
        return $path;
    }

}
