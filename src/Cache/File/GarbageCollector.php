<?php


namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Common\ObjectifiedFunctions;

class GarbageCollector {

    /**
     * @var string
     */
    private $cacheRoot;

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

    public function __construct(array $config, ObjectifiedFunctions $functions = null) {
        $this->cacheRoot = $config['cacheRoot'];
        $this->gcMaxAge = $config['garbageCollection']['maxAge'];
        $this->gcProbability = $config['garbageCollection']['probability'];
        $this->gcMaxItems = $config['garbageCollection']['maxItems'];

        if ($functions) {
            $this->functions = $functions;
        } else {
            $this->functions = new ObjectifiedFunctions();
        }
    }

    public function __destruct() {
        if ($this->shouldCollectGarbage()) {
            $this->collectGarbage();
        }
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
                foreach ($this->getOldFiles($this->getDirectoryIterator($file)) as $dirFile) {
                    yield $dirFile;
                }
            } else if (@$this->functions->filemtime($file) < $maxModificationTime) {
                yield $file;
            }
        }
    }

}
