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
        shuffle($files);
        $deleted = 0;
        foreach ($this->getOldFiles($files) as $file) {
            @$this->functions->unlink($file->getRealPath());
            $deleted++;
            if ($deleted == $this->gcMaxItems) {
                break;
            }
        }
    }

    private function getFiles($path) {
        $files = [];
        /** @var \SplFileInfo $item */
        foreach ($this->makeFileSystemIterator($path) as $item) {
            if ($this->isShard($item)) {
                $files = array_merge($files, $this->getFiles($item->getRealPath()));
            } else if ($this->isCacheEntry($item)) {
                $files[] = $item;
            }
        }
        return $files;
    }

    /**
     * @param \SplFileInfo[] $files
     * @return \Generator
     */
    private function getOldFiles(array $files) {
        $maxTimeModified = time() - $this->gcMaxAge;
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
            return new \FilesystemIterator($path);
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
