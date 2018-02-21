<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter extends BaseHTMLStreamFilter implements HTMLFilter {

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

    protected function handleTag(Tag $tag) {
        if ($tag->getTagName() == 'style') {
            $tag->setTextContent(
                $this->rewriter->rewriteStyle($tag->getTextContent())
            );
        } else if ($tag->hasAttribute('style')) {
            $tag->setAttribute(
                'style',
                $this->rewriter->rewriteStyle($tag->getAttribute('style'))
            );
        }
        yield $tag;
    }

}
