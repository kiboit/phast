<?php


namespace Kibo\Phast\Cache\File;


use Kibo\Phast\Common\ObjectifiedFunctions;

class DiskCleanup extends ProbabilisticExecutor {

    /**
     * @var integer
     */
    private $maxSize;

    /**
     * @var float
     */
    private $portionToFree;

    public function __construct(array $config, ObjectifiedFunctions $functions = null) {
        $this->maxSize = $config['diskCleanup']['maxSize'];
        $this->probability = $config['diskCleanup']['probability'];
        $this->portionToFree = $config['diskCleanup']['portionToFree'];
        parent::__construct($config, $functions);
    }

    protected function execute() {
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
