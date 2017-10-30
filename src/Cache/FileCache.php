<?php

namespace Kibo\Phast\Cache;

class FileCache implements Cache {

    /**
     * @var string
     */
    private $cacheRoot;

    /**
     * @var string
     */
    private $cacheNS;

    /**
     * @var integer
     */
    private $expirationTime;

    /**
     * FileCache constructor.
     *
     * @param string $cacheRoot
     * @param string $cacheNamespace
     * @param integer $expirationTime
     */
    public function __construct($cacheRoot, $cacheNamespace, $expirationTime) {
        $this->cacheRoot = $cacheRoot;
        $this->cacheNS = $cacheNamespace;
        $this->expirationTime = $expirationTime;
    }

    public function get($key, callable $cached) {
        $contents = $this->getFromCache($key);
        if (!is_null($contents)) {
            return $contents;
        }
        $contents = $cached();
        $this->storeCache($key, $contents);
        return $contents;
    }

    private function getCacheDir($key) {
        return $this->cacheRoot . '/' . ltrim($this->cacheNS, '/') . '/' . substr($key, 0, 2);
    }

    private function getCacheFilename($key) {
        return $this->getCacheDir($key) . '/' . $key;
    }

    private function storeCache($key, $contents) {
        $dir = $this->getCacheDir($key);
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
        $file = $this->getCacheFilename($key);
        $tmpFile = $file . '.' . uniqid('', true);
        @file_put_contents($tmpFile, $contents);
        @rename($tmpFile, $file);
        @chmod($file, 0777);
    }

    private function getFromCache($key) {
        $file = $this->getCacheFilename($key);
        if (file_exists($file) && filemtime($file) + $this->expirationTime > time()) {
            $contents = @file_get_contents($file);
            return $contents;
        }
        return null;
    }
}
