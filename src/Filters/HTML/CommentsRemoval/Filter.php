<?php


namespace Kibo\Phast\Filters\HTML\CommentsRemoval;

use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Comment;

class Filter implements HTMLStreamFilter {
    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        foreach ($elements as $element) {
            if (!($element instanceof Comment) || $element->isIEConditional()) {
                yield $element;
            }
        }
    }
}
