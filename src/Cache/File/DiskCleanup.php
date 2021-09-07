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

    /** @var array */
    private $keepNamespaces;

    public function __construct(array $config, ObjectifiedFunctions $functions = null) {
        $this->maxSize = $config['diskCleanup']['maxSize'];
        $this->probability = $config['diskCleanup']['probability'];
        $this->portionToFree = $config['diskCleanup']['portionToFree'];
        $this->keepNamespaces = $config['diskCleanup']['keepNamespaces'];
        parent::__construct($config, $functions);
    }

    protected function execute() {
        $usedSpace = $this->calculateUsedSpace();
        $neededSpace = round($this->portionToFree * $this->maxSize);
        $bytesToDelete = $usedSpace - $this->maxSize + $neededSpace;
        $deletedBytes = 0;
        /** @var \SplFileInfo $file */
        foreach ($this->getCacheFiles($this->cacheRoot) as $file) {
            if ($deletedBytes >= $bytesToDelete) {
                break;
            }
            $deletedBytes += $file->getSize();
            @unlink($file->getRealPath());
        }
    }

    private function calculateUsedSpace() {
        $size = 0;
        /** @var \SplFileInfo $file */
        foreach ($this->getCacheFiles($this->cacheRoot) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    protected function getCacheFiles($root) {
        foreach (parent::getCacheFiles($root) as $item) {
            if (!preg_match('~^[a-f0-9]{32}-(.+)$~', $item->getFilename(), $match)
                || in_array($match[1], $this->keepNamespaces)
            ) {
                continue;
            }
            yield $item;
        }
    }
}
