<?php


namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Common\ObjectifiedFunctions;

class GarbageCollector extends ProbabilisticExecutor {

    /**
     * @var integer
     */
    private $shardingDepth;

    /**
     * @var integer
     */
    private $gcMaxAge;

    /**
     * @var integer
     */
    private $gcMaxItems;


    public function __construct(array $config, ObjectifiedFunctions $functions = null) {
        $this->shardingDepth = $config['shardingDepth'];
        $this->gcMaxAge = $config['garbageCollection']['maxAge'];
        $this->gcMaxItems = $config['garbageCollection']['maxItems'];
        $this->probability = $config['garbageCollection']['probability'];
        parent::__construct($config, $functions);
    }

    protected function execute() {
        $files = $this->getFiles($this->cacheRoot);
        $deleted = 0;
        /** @var \SplFileInfo $file */
        foreach ($this->getOldFiles($files) as $file) {
            @$this->functions->unlink($file->getRealPath());
            $deleted++;
            if ($deleted == $this->gcMaxItems) {
                break;
            }
        }
    }

    private function getFiles($path) {
        /** @var \SplFileInfo $item */
        foreach ($this->makeFileSystemIterator($path) as $item) {
            if ($this->isShard($item)) {
                foreach ($this->getFiles($item->getRealPath()) as $item) {
                    yield $item;
                }
            } else if ($this->isCacheEntry($item)) {
                yield $item;
            }
        }
    }

    /**
     * @param \Iterator $files
     * @return \Generator
     */
    private function getOldFiles($files) {
        $maxTimeModified = time() - $this->gcMaxAge;
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getMTime() < $maxTimeModified) {
                yield $file;
            }
        }
    }

    /**
     * @return \Iterator
     */
    private function makeFileSystemIterator($path) {
        try {
            $items = iterator_to_array(new \FilesystemIterator($path));
            shuffle($items);
            return new \ArrayIterator($items);
        } catch (\Exception $e) {
            return new \ArrayIterator([]);
        }
    }

    private function isShard(\SplFileInfo $item) {
        return $item->isDir()
                && !$item->isLink()
                && preg_match('/^[a-f\d]{2}$/', $item->getFilename());
    }

    private function isCacheEntry(\SplFileInfo $item) {
        return $item->isFile() && preg_match('/^[a-f\d]{32}-.*$/', $item->getFilename());
    }

}
