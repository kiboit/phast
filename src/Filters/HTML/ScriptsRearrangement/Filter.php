<?php

namespace Kibo\Phast\Filters\HTML\ScriptsRearrangement;

use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter {
    use JSDetectorTrait;

    /**
     * @var Tag[]
     */
    private $scripts = [];

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        foreach ($elements as $element) {
            if ($this->isScript($element)) {
                $this->scripts[] = $element;
                continue;
            }
            if ($this->isClosingBody($element)) {
                foreach ($this->scripts as $script) {
                    yield $script;
                }
                $this->scripts = [];
            }
            yield $element;
        }
        foreach ($this->scripts as $script) {
            yield $script;
        }
    }

    private function isScript(Element $element) {
        return $element instanceof Tag && $element->getTagName() == 'script' && $this->isJSElement($element);
    }

    private function isClosingBody(Element $element) {
        return $element instanceof ClosingTag && $element->getTagName() == 'body';
    }
}
