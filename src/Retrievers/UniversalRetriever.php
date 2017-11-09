<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\ValueObjects\URL;

class UniversalRetriever implements Retriever {

    /**
     * @var Retriever[]
     */
    private $retrievers = [];

    public function retrieve(URL $url) {
        foreach ($this->retrievers as $retriever) {
            $result = $retriever->retrieve($url);
            if ($result !== false) {
                return $result;
            }
        }
        return false;
    }

    public function getLastModificationTime(URL $url) {
        throw new \RuntimeException('Not implemented');
    }

    public function addRetriever(Retriever $retriever) {
        $this->retrievers[] = $retriever;
    }

}
