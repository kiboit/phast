<?php
namespace Kibo\Phast\Filters\HTML\MetaCharset;

use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter {
    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $didYield = false;
        foreach ($elements as $element) {
            if ($element instanceof Tag) {
                if ($element->tagName == 'meta'
                    && array_keys($element->getAttributes()) == ['charset']
                ) {
                    continue;
                }
                if (!$didYield && !in_array($element->tagName, ['html', 'head', '!doctype'])) {
                    yield new Tag('meta', ['charset' => 'utf-8']);
                    $didYield = true;
                }
            }
            yield $element;
        }
    }
}
