<?php


namespace Kibo\Phast\Filters\HTML;


use Kibo\Phast\Filters\HTML\Helpers\ElementsToDOMFilterAdapter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

abstract class BaseHTMLPageContextFilter implements HTMLStreamFilter, HTMLFilter {
    use ElementsToDOMFilterAdapter;

    /**
     * @var HTMLPageContext
     */
    protected $context;

    /**
     * @var \Traversable
     */
    protected $elements;

    /**
     * @param Tag $tag
     * @return Element
     */
    abstract protected function handleTag(Tag $tag);

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $this->context = $context;
        $this->elements = $elements;
        $this->beforeLoop();
        foreach ($this->elements as $element) {
            if (($element instanceof Tag) && $this->isTagOfInterest($element)) {
                foreach ($this->handleTag($element) as $item) {
                    yield $item;
                }
            } else {
                yield $element;
            }
        }
        $this->afterLoop();
    }

    /**
     * @param Tag $tag
     * @return bool
     */
    protected function isTagOfInterest(Tag $tag) {
        return true;
    }


    protected function beforeLoop() {}

    protected function afterLoop() {}


}
