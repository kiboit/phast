<?php
namespace Kibo\Phast\Filters\HTML\Minify;

use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Junk;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter {
    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $inTags = [
            'pre' => 0,
            'textarea' => 0,
        ];
        foreach ($elements as $element) {
            if ($element instanceof Tag && isset($inTags[$element->getTagName()])) {
                $inTags[$element->getTagName()]++;
            } elseif ($element instanceof ClosingTag && !empty($inTags[$element->getTagName()])) {
                $inTags[$element->getTagName()]--;
            } elseif ($element instanceof Junk && !array_sum($inTags)) {
                $element->originalString = preg_replace_callback('~\s++~', function ($match) {
                    return strpos($match[0], "\n") === false ? ' ' : "\n";
                }, $element->originalString);
            }
            yield $element;
        }
    }
}
