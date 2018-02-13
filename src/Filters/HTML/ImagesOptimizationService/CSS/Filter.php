<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

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
        $styleTags = $document->getElementsByTagName('style');
        /** @var \DOMElement $styleTag */
        foreach ($styleTags as $styleTag) {
            $styleTag->textContent = $this->rewriter->rewriteStyle($styleTag->textContent);
        }

        $tags = $document->getElementsWithAttr('style');
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $tag->setAttribute(
                'style',
                $this->rewriter->rewriteStyle($tag->getAttribute('style'))
            );
        }
    }
}
