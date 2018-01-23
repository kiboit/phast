<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;

class Filter implements HTMLFilter {

    /**
     * @var ImageURLRewriter
     */
    protected $rewriter;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $styleTags = $document->query('//style');
        /** @var \DOMElement $styleTag */
        foreach ($styleTags as $styleTag) {
            $styleTag->textContent = $this->rewriter->rewriteStyle($styleTag->textContent);
        }

        $styleAttrs = $document->query('//@style');
        /** @var \DOMAttr $styleAttr */
        foreach ($styleAttrs as $styleAttr) {
            $styleAttr->value = htmlspecialchars($this->rewriter->rewriteStyle($styleAttr->value));
        }
    }
}
