<?php

namespace Kibo\Phast\Cache;

use Kibo\Phast\Common\ObjectifiedFunctions;

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

    /**
     * @var float
     */
    private $gcProbability;

    /**
     * @var integer
     */
    private $gcMaxItems;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;


    public function __construct(array $config, $cacheNamespace, ObjectifiedFunctions $functions = null) {
        $this->cacheRoot = $config['cacheRoot'];
        $this->maxAge = $config['cacheMaxAge'];
        $this->gcProbability = $config['garbageCollection']['probability'];
        $this->gcMaxItems = $config['garbageCollection']['maxItems'];
        $this->cacheNS = $cacheNamespace;

        if ($functions) {
            $this->functions = $functions;
        } else {
            $this->functions = new ObjectifiedFunctions();
        }

        if ($this->shouldCollectGarbage()) {
            $this->functions->register_shutdown_function(function () {
                $this->collectGarbage();
            });
        }
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
        return $this->cacheRoot . '/' . ltrim($this->cacheNS, '/') . '/' . substr($this->getHashedKey($key), 0, 2);
    }

    private function getCacheFilename($key) {
        return $this->getCacheDir($key) . '/' . $this->getHashedKey($key);
    }

    private function getHashedKey($key) {
        return md5($key);
    }

    private function storeCache($key, $contents) {
        $dir = $this->getCacheDir($key);
        if (!file_exists($dir)) {
            @mkdir($dir, 0700, true);
        }
        if (posix_geteuid() !== fileowner($this->cacheRoot)) {
            $this->functions->error_log(
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
            $this->functions->error_log(
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
            $this->functions->error_log("Phast cache error: Could not read file $file");
        }
        return null;
    }

    private function shouldCollectGarbage() {
        if ($this->gcProbability <= 0) {
            return false;
        }
        if ($this->gcProbability >= 1) {
            return true;
        }
        return $this->functions->mt_rand(1, round(1 /  $this->gcProbability)) == 1;
    }

    private function collectGarbage() {
        $dirs = $this->getDirectoryIterator($this->cacheRoot);
        $fileIterators = [];
        foreach ($dirs as $dir) {
            $fileIterators[] = $this->getOldFiles($this->getDirectoryIterator($dir));
        }
        shuffle($fileIterators);
        $deleted = 0;
        while ($deleted < $this->gcMaxItems && count($fileIterators)) {
            foreach ($fileIterators as $idx => $iterator) {
                $file = $iterator->current();
                if ($file) {
                    $this->functions->unlink($file);
                    $iterator->next();
                    $deleted++;
                } else {
                    unset ($fileIterators[$idx]);
                }
            }
        }
    }

    private function getDirectoryIterator($path) {
        $dir = $this->functions->opendir($path);
        while (($item = $this->functions->readdir($dir)) !== false) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $full = $path . '/' . $item;
            yield $full;
        }
    }


    private function getOldFiles($files) {
        $maxModificationTime = $this->functions->time() - $this->maxAge;
        foreach ($files as $file) {
            if ($this->functions->is_dir($file)) {
                continue;
            }
            if ($this->functions->filemtime($file) < $maxModificationTime) {
                yield $file;
            }
        }
    }

}
