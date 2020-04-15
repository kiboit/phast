<?php

namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Cache\Cache as CacheInterface;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Common\System;
use Kibo\Phast\Logging\LoggingTrait;

class Cache implements CacheInterface {
    use LoggingTrait;

    const VERSION = '2';

    /**
     * @var GarbageCollector
     */
    private static $garbageCollector;

    /**
     * @var DiskCleanup
     */
    private static $diskCleanup;

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
    private $shardingDepth;

    /**
     * @var integer
     */
    private $gcMaxAge;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * @var System
     */
    private $system;

    public function __construct(array $config, $cacheNamespace, ObjectifiedFunctions $functions = null) {
        $this->cacheRoot = $config['cacheRoot'];
        $this->shardingDepth = $config['shardingDepth'];
        $this->gcMaxAge = $config['garbageCollection']['maxAge'];
        $this->cacheNS = $cacheNamespace;

        if ($functions) {
            $this->functions = $functions;
        } else {
            $this->functions = new ObjectifiedFunctions();
        }

        $this->system = new System($this->functions);

        if (!isset(self::$garbageCollector)) {
            self::$garbageCollector = new GarbageCollector($config, $this->functions);
            self::$diskCleanup = new DiskCleanup($config, $this->functions);
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

    public function set($key, $value, $expiresIn = 0) {
        $this->storeCache($key, $value, $expiresIn);
    }

    /**
     * @return GarbageCollector
     */
    public function getGarbageCollector() {
        return self::$garbageCollector;
    }

    /**
     * @return DiskCleanup
     */
    public function getDiskCleanup() {
        return self::$diskCleanup;
    }

    private function getCacheDir($key) {
        $hashedKey = $this->getHashedKey($key);
        $parts = [$this->cacheRoot];
        for ($i = 0; $i < $this->shardingDepth * 2; $i += 2) {
            $parts[] = substr($hashedKey, $i, 2);
        }
        return join('/', $parts);
    }

    private function getCacheFilename($key) {
        return $this->getCacheDir($key) . '/' . $this->getHashedKey($key) . '-' . ltrim($this->cacheNS, '/');
    }

    private function getHashedKey($key) {
        return md5($key);
    }

    private function storeCache($key, $contents, $expiresIn) {
        $dir = $this->getCacheDir($key);
        if (!file_exists($dir)) {
            @mkdir($dir, 0700, true);
        }
        if (($uid = $this->system->getUserId())
            && $uid !== $this->functions->fileowner($this->cacheRoot)
        ) {
            $this->logger()->critical(
                'Phast: FileCache: Cache root {cacheRoot} owned by {fileOwner}, but process user is {userId}!',
                [
                    'cacheRoot' => $this->cacheRoot,
                    'fileOwner' => fileowner($this->cacheRoot),
                    'userId' => $uid,
                ]
            );
            return;
        }
        $file = $this->getCacheFilename($key);
        $tmpFile = $file . '.' . uniqid('', true);
        $expirationTime = $expiresIn > 0 ? $this->functions->time() + $expiresIn : 0;
        $serialized = $expirationTime . ' ' . self::VERSION . ' ' . serialize($contents);
        $result = @$this->functions->file_put_contents($tmpFile, $serialized);
        if ($result !== strlen($serialized)) {
            $this->logger()->critical(
                'Phast: FileCache: Error writing to file {filename}. {written} of {total} bytes written!',
                [
                    'filename' => $tmpFile,
                    'written' => json_encode($result),
                    'total' => strlen($serialized),
                ]
            );
            @unlink($tmpFile);
            return;
        }
        @chmod($tmpFile, 0400);
        @rename($tmpFile, $file);
    }

    private function getFromCache($key) {
        $file = $this->getCacheFilename($key);
        if (!@$this->functions->file_exists($file)) {
            return null;
        }
        $contents = @file_get_contents($file);
        if ($contents === false) {
            $this->logger()->critical('Phast: FileCache: Could not read file {file}', ['file' => $file]);
            return null;
        }
        list($expirationTime, $version, $data) = explode(' ', $contents, 3);
        if ($version !== self::VERSION) {
            $this->logger()->debug('Phast: FileCache: Refusing to read old cache file {file}', ['file' => $file]);
            return null;
        }
        if ($expirationTime > $this->functions->time() || $expirationTime == 0) {
            if (time() - @$this->functions->filectime($file) >= round($this->gcMaxAge / 10)) {
                $this->functions->touch($file);
            }
            return unserialize($data);
        }
        return null;
    }
}
