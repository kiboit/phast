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
        $fileIterators = $this->getOldFilesIterators($this->cacheRoot);
        shuffle($fileIterators);
        $deleted = 0;
        while ($deleted < $this->gcMaxItems && count($fileIterators)) {
            foreach ($fileIterators as $idx => $iterator) {
                $file = $iterator->current();
                if ($file) {
                    $this->functions->unlink($file->getRealPath());
                    $iterator->next();
                    $deleted++;
                } else {
                    unset ($fileIterators[$idx]);
                }
            }
        }
    }

    private function getOldFilesIterators($path, $depth = 0) {
        $dir = @opendir($path);
        $iterators = [];
        if (!$dir) {
            return $iterators;
        }
        if ($depth == $this->shardingDepth) {
            return [$this->makeOldFilesIterator($path)];
        }
        while (($item = readdir($dir)) !== false) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $fullPath = $path . '/' . $item;
            if (is_dir($fullPath)) {
                $iterators = array_merge($iterators, $this->getOldFilesIterators($fullPath, $depth + 1));
            }
        }
        return $iterators;
    }

    private function makeOldFilesIterator($path) {
        $entries = new \FilesystemIterator($path);
        foreach ($entries as $file) {
            if ($file->isFile() && $file->getMTime() < $this->functions->time() - $this->gcMaxAge) {
                yield $file;
            }
        }
    }

}
