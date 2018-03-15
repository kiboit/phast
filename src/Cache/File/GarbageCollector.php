<?php


namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Common\ObjectifiedFunctions;

class GarbageCollector extends ProbabilisticExecutor {

    /**
     * @var integer
     */
    private $gcMaxAge;

    /**
     * @var integer
     */
    private $gcMaxItems;


    public function __construct(array $config, ObjectifiedFunctions $functions = null) {
        $this->gcMaxAge = $config['garbageCollection']['maxAge'];
        $this->gcMaxItems = $config['garbageCollection']['maxItems'];
        $this->probability = $config['garbageCollection']['probability'];
        parent::__construct($config, $functions);
    }

    protected function execute() {
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
                foreach ($this->getOldFiles($this->getDirectoryIterator($file)) as $dirFile) {
                    yield $dirFile;
                }
            } else if (@$this->functions->filemtime($file) < $maxModificationTime) {
                yield $file;
            }
        }
    }

}
