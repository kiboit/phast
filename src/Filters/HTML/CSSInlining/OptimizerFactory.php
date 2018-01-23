<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;


use Kibo\Phast\Common\DOMDocument;

class OptimizerFactory {

    /**
     * @param DOMDocument $document
     * @return Optimizer
     */
    public function makeForDocument(DOMDocument $document) {
        return new Optimizer($document);
    }

}
