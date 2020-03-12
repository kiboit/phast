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
        $files = $this->getCacheFiles($this->cacheRoot);
        $deleted = 0;
        /** @var \SplFileInfo $file */
        foreach ($this->filterOldFiles($files) as $file) {
            @$this->functions->unlink($file->getRealPath());
            $deleted++;
            if ($deleted == $this->gcMaxItems) {
                break;
            }
        }
    }

    /**
     * @param \Iterator $files
     * @return \Generator
     */
    private function filterOldFiles(\Iterator $files) {
        $maxTimeModified = time() - $this->gcMaxAge;
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getMTime() < $maxTimeModified) {
                yield $file;
            }
        }
    }
}
