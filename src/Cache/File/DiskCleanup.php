<?php


namespace Kibo\Phast\Cache\File;


class DiskCleanup {

    /**
     * @var string
     */
    private $cacheRoot;

    /**
     * @var integer
     */
    private $shardingDepth;

    /**
     * @var integer
     */
    private $maxSize;

    /**
     * @var float
     */
    private $probability;

    /**
     * @var integer
     */
    private $sampleFilesCount;

    /**
     * @var float
     */
    private $portionToFree;

    public function __construct(array $config) {
        $this->cacheRoot = $config['cacheRoot'];
        $this->shardingDepth = $config['shardingDepth'];
        $this->maxSize = $config['diskCleanup']['maxSize'];
        $this->probability = $config['diskCleanup']['probability'];
        $this->sampleFilesCount = $config['diskCleanup']['sampleFilesCount'];
        $this->portionToFree = $config['diskCleanup']['portionToFree'];
        $this->cleanup();
    }

    private function cleanup() {
        $usedSpace = $this->calculateUsedSpace();
        $neededSpace = round($this->portionToFree * $this->maxSize);
        $bytesToDelete = $usedSpace - $this->maxSize + $neededSpace;
        $deletedBytes = 0;
        $condition = function () use (&$deletedBytes, $bytesToDelete) {
            return $deletedBytes < $bytesToDelete;
        };
        $callback = function (\SplFileInfo $file) use (&$deletedBytes) {
            $deletedBytes += $file->getSize();
            unlink($file->getRealPath());
        };
        $this->walkDirectory($condition, $callback);
    }

    private function calculateUsedSpace() {
        $size = 0;
        $condition = function () use (&$size, &$filesCount) {
            return true;
        };

        $callback = function (\SplFileInfo $file) use (&$size) {
            $size += $file->getSize();
        };
        $this->walkDirectory($condition, $callback);
        return $size;
    }

    private function walkDirectory(callable $condition, callable $callback) {
        $dirsIterator = new \RecursiveDirectoryIterator($this->cacheRoot);
        $iterator = new \RecursiveIteratorIterator($dirsIterator);
        while ($iterator->valid() && $condition()) {
            /** @var \SplFileInfo $file */
            $file = $iterator->current();
            if (!$file->isDir()) {
                $callback($file);
            }
            $iterator->next();
        }
    }


}
