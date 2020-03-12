<?php


namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Common\ObjectifiedFunctions;

abstract class ProbabilisticExecutor {
    /**
     * @var string
     */
    protected $cacheRoot;

    /**
     * @var float
     */
    protected $probability = 0;

    /**
     * @var ObjectifiedFunctions
     */
    protected $functions;

    abstract protected function execute();

    protected function __construct(array $config, ObjectifiedFunctions $functions = null) {
        $this->cacheRoot = $config['cacheRoot'];
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    public function __destruct() {
        if ($this->shouldExecute()) {
            $this->execute();
        }
    }

    private function shouldExecute() {
        if (!$this->functions->file_exists($this->cacheRoot)) {
            return false;
        }
        if ($this->probability <= 0) {
            return false;
        }
        if ($this->probability >= 1) {
            return true;
        }
        return $this->functions->mt_rand(1, round(1 /  $this->probability)) == 1;
    }

    protected function getCacheFiles($path) {
        /** @var \SplFileInfo $item */
        foreach ($this->makeFileSystemIterator($path) as $item) {
            if ($this->isShard($item)) {
                foreach ($this->getCacheFiles($item->getRealPath()) as $item) {
                    yield $item;
                }
            } elseif ($this->isCacheEntry($item)) {
                yield $item;
            }
        }
    }

    /**
     * @return \Iterator
     */
    protected function makeFileSystemIterator($path) {
        try {
            $items = iterator_to_array(new \FilesystemIterator($path));
            shuffle($items);
            return new \ArrayIterator($items);
        } catch (\Exception $e) {
            return new \ArrayIterator([]);
        }
    }

    protected function isShard(\SplFileInfo $item) {
        return $item->isDir()
            && !$item->isLink()
            && preg_match('/^[a-f\d]{2}$/', $item->getFilename());
    }

    protected function isCacheEntry(\SplFileInfo $item) {
        return $item->isFile() && preg_match('/^[a-f\d]{32}-/', $item->getFilename());
    }
}
