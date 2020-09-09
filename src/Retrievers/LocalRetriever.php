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

    public static function getAllowedExtensions() {
        return ['css', 'js', 'bmp', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'ico', 'svg', 'txt'];
    }

    public function retrieve(URL $url) {
        return $this->guard($url, function ($file) {
            return @$this->funcs->file_get_contents($file);
        });
    }

    public function getCacheSalt(URL $url) {
        return $this->guard($url, function ($file) {
            $size = @$this->funcs->filesize($file);
            $mtime = @$this->funcs->filectime($file);
            if ($size === false && $mtime === false) {
                return '';
            }
            return "$mtime-$size";
        });
    }

    public function getSize(URL $url) {
        return $this->guard($url, function ($file) {
            return @$this->funcs->filesize($file);
        });
    }

    private function guard(URL $url, callable $cb) {
        if (!in_array($this->getExtensionForURL($url), self::getAllowedExtensions())) {
            return false;
        }
        $file = $this->getFileForURL($url);
        if ($file === false) {
            return false;
        }
        return $cb($file);
    }

    private function getExtensionForURL(URL $url) {
        $dotPosition = strrpos($url->getDecodedPath(), '.');
        if ($dotPosition === false) {
            return '';
        }
        return strtolower(substr($url->getDecodedPath(), $dotPosition + 1));
    }

    private function getFileForURL(URL $url) {
        if (!isset($this->map[$url->getHost()])) {
            return false;
        }
        $submap = $this->map[$url->getHost()];
        if (!is_array($submap)) {
            return $this->appendNormalized($submap, $url->getDecodedPath());
        }

        $selectedPath = null;
        $selectedRoot = null;

        foreach ($submap as $prefix => $root) {
            $pattern = '~^(?=/)/*?(?:' . str_replace('~', '\\~', $prefix) . ')(?<path>/*(?<=/).*)~';
            if (preg_match($pattern, $url->getDecodedPath(), $match)
                && ($selectedPath === null || strlen($match['path']) < strlen($selectedPath))
            ) {
                $selectedRoot = $root;
                $selectedPath = $match['path'];
            }
        }

        if ($selectedPath === null) {
            return false;
        }

        return $this->appendNormalized($selectedRoot, $selectedPath);
    }

    private function appendNormalized($target, $appended) {
        $appended = explode("\0", $appended)[0];
        $appended = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $appended);

        $absolutes = [];

        foreach (explode(DIRECTORY_SEPARATOR, $appended) as $part) {
            if ($part == '' || $part == '.') {
            } elseif ($part == '..') {
                if (array_pop($absolutes) === null) {
                    return false;
                }
            } else {
                $absolutes[] = $part;
            }
        }

        return $target . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $absolutes);
    }
}
