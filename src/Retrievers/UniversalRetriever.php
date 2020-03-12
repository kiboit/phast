<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

class UniversalRetriever implements Retriever {
    /**
     * @var Retriever[]
     */
    private $retrievers = [];

    public function retrieve(URL $url) {
        return $this->iterateRetrievers(function (Retriever $retriever) use ($url) {
            return $retriever->retrieve($url);
        });
    }

    public function getCacheSalt(URL $url) {
        return $this->iterateRetrievers(function (Retriever $retriever) use ($url) {
            return $retriever->getCacheSalt($url);
        });
    }

    private function iterateRetrievers(callable $callback) {
        foreach ($this->retrievers as $retriever) {
            $result = $callback($retriever);
            if ($result !== false) {
                return $result;
            }
        }
        return false;
    }

    public function addRetriever(Retriever $retriever) {
        $this->retrievers[] = $retriever;
    }
}
