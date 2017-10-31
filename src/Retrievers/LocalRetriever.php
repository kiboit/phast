<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\ValueObjects\URL;

class LocalRetriever implements Retriever {

    /**
     * @var array
     */
    private $map;

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    /**
     * LocalRetriever constructor.
     *
     * @param array $map
     * @param ObjectifiedFunctions|null $functions
     */
    public function __construct(array $map, ObjectifiedFunctions $functions = null) {
        $this->map = $map;
        if ($functions) {
            $this->funcs = $functions;
        } else {
            $this->funcs = new ObjectifiedFunctions();
        }
    }

    public function retrieve(URL $url) {
        $file = $this->getFileForURL($url);
        if ($file === false) {
            return false;
        }
        return @$this->funcs->file_get_contents($file);
    }

    public function getLastModificationTime(URL $url) {
        $file = $this->getFileForURL($url);
        if ($file === false) {
            return false;
        }
        return @$this->funcs->filemtime($file);
    }

    private function getFileForURL(URL $url) {
        if (!isset ($this->map[$url->getHost()])) {
            return false;
        }
        $base = URL::fromString($this->map[$url->getHost()]);
        $base = $this->normalizePath($base);
        if (!$base) {
            return false;
        }
        $path = $this->normalizePath($base . '/' . $url->getPath());
        if (!$path) {
            return false;
        }
        if (strpos($path, $base) !== 0) {
            return false;
        }
        return $path;
    }

    private function normalizePath($path) {
        $path = explode("\0", $path)[0];
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $absolutes = array();
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        if ($parts[0] == '') {
            $absolutes[] = '';
        }
        foreach ($parts as $part) {
            if ('.' == $part || '' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

}
