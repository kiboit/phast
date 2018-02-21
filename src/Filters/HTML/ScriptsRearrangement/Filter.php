<?php

namespace Kibo\Phast\Filters\HTML\ScriptsRearrangement;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\Helpers\ElementsToDOMFilterAdapter;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter, HTMLFilter {
    use JSDetectorTrait, ElementsToDOMFilterAdapter;

    /**
     * @var Tag[]
     */
    private $scripts = [];

    /**
     * @var bool
     */
    private $foundBody = false;

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        foreach ($elements as $element) {
            if ($this->isScript($element)) {
                $this->scripts[] = $element;
                continue;
            }
            if ($this->isClosingBody($element)) {
                $this->foundBody = true;
                foreach ($this->scripts as $script) {
                    yield $script;
                }
            }
            yield $element;
        }
        if (!$this->foundBody) {
            throw new \Exception('No closing body tag found in document');
        }
    }

    private function isScript(Element $element) {
        return $element instanceof Tag && $element->getTagName() == 'script' && $this->isJSElement($element);
    }

    private function isClosingBody(Element $element) {
        return $element instanceof ClosingTag && $element->getTagName() == 'body';
    }

    protected function getElementsToRearrange(DOMDocument $document) {
        $scripts = $document->query('//script');
        foreach ($scripts as $script) {
            if ($this->isJSElement($script)) {
                yield $script;
            }
        }
    }
}
