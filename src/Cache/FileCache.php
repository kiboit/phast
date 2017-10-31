<?php

namespace Kibo\Phast\Cache;

class FileCache implements Cache {

    /**
     * @var string
     */
    private $cacheRoot;

    /**
     * @var integer
     */
    private $maxAge;

    /**
     * @var string
     */
    private $cacheNS;


    public function __construct(array $config, $cacheNamespace) {
        $this->cacheRoot = $config['cacheRoot'];
        $this->maxAge = $config['cacheMaxAge'];
        $this->cacheNS = $cacheNamespace;
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
            @mkdir($dir, 0700, true);
        }
        if (posix_geteuid() !== fileowner($this->cacheRoot)) {
            error_log(
                sprintf(
                    'Phast cache error: Cache root %s owned by %s process user is %s!',
                    $this->cacheRoot,
                    fileowner($this->cacheRoot),
                    posix_geteuid()
                )
            );
            return;
        }
        $file = $this->getCacheFilename($key);
        $tmpFile = $file . '.' . uniqid('', true);
        $serialized = serialize($contents);
        $result = @file_put_contents($tmpFile, $serialized);
        if ($result !== strlen($serialized)) {
            error_log(
                sprintf(
                    'Phast cache error: Error writing to file %s. %s of %s bytes written!',
                    $tmpFile,
                    (int)$result,
                    strlen($serialized)
                )
            );
            return;
        }
        @rename($tmpFile, $file);
        @chmod($file, 0700);
    }

    private function getFromCache($key) {
        $file = $this->getCacheFilename($key);
        if (file_exists($file) && filemtime($file) + $this->maxAge > time()) {
            $contents = @file_get_contents($file);
            if ($contents !== false) {
                return unserialize($contents);
            }
            error_log("Phast cache error: Could not read file $file");
        }
        return null;
    }
}
