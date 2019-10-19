<?php
namespace Kibo\Phast\Filters\HTML\MinifyScripts;

use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter {
    use JSDetectorTrait;

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $inTags = [
            'pre' => 0,
            'textarea' => 0,
        ];
        foreach ($elements as $element) {
            if ($element instanceof Tag
                && $element->getTagName() === 'script'
                && ($content = $element->getTextContent()) !== ''
            ) {
                $content = trim($content);
                if ($this->isJSElement($element)) {
                    $content = (new JSMinifier($content, true))->min();
                } elseif(($data = @json_decode($content)) !== null
                         && ($newContent = json_encode($data,
                                 JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false
                ) {
                    $content = str_replace('</', '<\\/', $newContent);
                }
                $element->setTextContent($content);
            }
            yield $element;
        }
    }

}
