<?php

namespace Kibo\Phast\Cache\File;

abstract class ProbabilisticExecutorTestCase extends CacheTestCase {
    /**
     * @return ProbabilisticExecutor
     */
    abstract protected function makeExecutor();

    /**
     * @param float $probability
     */
    abstract protected function setProbability($probability);

    abstract protected function setUpCacheContents();

    abstract protected function getCacheContents();

    public function testExecutionRunCalculation() {
        $actualMin = $actualMax = null;
        $this->functions->mt_rand = function ($min, $max) use (&$actualMin, &$actualMax) {
            $actualMin = $min;
            $actualMax = $max;
        };
        $this->functions->file_exists = function () {
            return true;
        };
        $this->setProbability(0.5);
        $this->makeExecutor();
        $this->assertEquals(1, $actualMin);
        $this->assertEquals(2, $actualMax);
    }

    public function testNotExplodingWhenExecutingOnMissingCacheRoot() {
        $this->makeExecutor();
    }

    public function testExecutionOnDestruction() {
        $this->setProbability(1);
        $this->setUpCacheContents();
        $contents = $this->getCacheContents();
        $gc = $this->makeExecutor();
        $this->assertEquals($contents, $this->getCacheContents());
        unset($gc);
        $this->assertNotEquals($contents, $this->getCacheContents());
    }
}
