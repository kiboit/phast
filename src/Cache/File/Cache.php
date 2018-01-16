<?php

namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Cache\Cache as CacheInterface;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LoggingTrait;

class Cache implements CacheInterface {
    use LoggingTrait;

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
    private $gcMaxAge;

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
        $this->gcMaxAge = $config['garbageCollection']['maxAge'];
        $this->gcProbability = $config['garbageCollection']['probability'];
        $this->gcMaxItems = $config['garbageCollection']['maxItems'];
        $this->cacheNS = $cacheNamespace;

        if ($functions) {
            $this->functions = $functions;
        } else {
            $this->functions = new ObjectifiedFunctions();
        }

        if ($this->shouldCollectGarbage()) {
            $this->collectGarbage();
        }
    }

    public function get($key, callable $cached = null, $expiresIn = 0) {
        $contents = $this->getFromCache($key);
        if (!is_null($contents)) {
            return $contents;
        }
        if (is_null($cached)) {
            return null;
        }
        $contents = $cached();
        $this->storeCache($key, $contents, $expiresIn);
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

    private function storeCache($key, $contents, $expiresIn) {
        $dir = $this->getCacheDir($key);
        if (!file_exists($dir)) {
            @mkdir($dir, 0700, true);
        }
        if ($this->functions->posix_geteuid() !== $this->functions->fileowner($this->cacheRoot)) {
            $this->logger()->critical(
                'Phast: FileCache: Cache root {cacheRoot} owned by {fileOwner}, but process user is {userId}!',
                [
                    'cacheRoot' => $this->cacheRoot,
                    'fileOwner' => fileowner($this->cacheRoot),
                    'userId' => posix_getuid()
                ]
            );
            return;
        }
        $file = $this->getCacheFilename($key);
        $tmpFile = $file . '.' . uniqid('', true);
        $expirationTime = $expiresIn > 0 ? $this->functions->time() + $expiresIn : 0;
        $serialized = $expirationTime . ' ' . json_encode($contents);
        $result = @$this->functions->file_put_contents($tmpFile, $serialized);
        if ($result !== strlen($serialized)) {
            $this->logger()->critical(
                'Phast: FileCache: Error writing to file {filename}. {written} of {total} bytes written!',
                [
                    'filename' => $tmpFile,
                    'written' => (int)$result,
                    'total' => strlen($serialized)
                ]
            );
            return;
        }
        @rename($tmpFile, $file);
        @chmod($file, 0700);
    }

    private function getFromCache($key) {
        $file = $this->getCacheFilename($key);
        if (!@$this->functions->file_exists($file)) {
            return null;
        }
        $contents = @file_get_contents($file);
        if ($contents === false) {
            $this->logger()->critical("Phast: FileCache: Could not read file {file}", ['file' => $file]);
            return null;
        }
        list ($expirationTime, $data) = explode(" ", $contents, 2);
        if ($expirationTime > $this->functions->time() || $expirationTime == 0) {
            if (time() - @$this->functions->filemtime($file) >= round($this->gcMaxAge / 10)) {
                $this->functions->touch($file);
            }
            return json_decode($data, true);
        }
        return null;
    }

    private function shouldCollectGarbage() {
        if (!$this->functions->file_exists($this->cacheRoot)) {
            return false;
        }
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
        $dir = @$this->functions->opendir($path);
        if (!$dir) {
            return;
        }
        while (($item = $this->functions->readdir($dir)) !== false) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $full = $path . '/' . $item;
            yield $full;
        }
    }


    private function getOldFiles($files) {
        $maxModificationTime = $this->functions->time() - $this->gcMaxAge;
        foreach ($files as $file) {
            if (@$this->functions->is_dir($file)) {
                continue;
            }
            if (@$this->functions->filemtime($file) < $maxModificationTime) {
                yield $file;
            }
        }
    }

}
