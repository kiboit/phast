<?php
namespace Kibo\Phast\Filters\HTML\MinifyScripts;

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Filter implements HTMLStreamFilter {
    use JSDetectorTrait;

    private $cache;

    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        foreach ($elements as $element) {
            if ($element instanceof Tag
                && $element->getTagName() === 'script'
                && ($content = $element->getTextContent()) !== ''
            ) {
                $content = trim($content);
                if ($this->isJSElement($element)
                    && preg_match('~[()[\]{};]\s~', $content)
                ) {
                    $content = preg_replace('~^\s*<!--\s*\n(.*)\n\s*-->\s*$~s', '$1', $content);
                    $content = $this->cache->get(md5($content), function () use ($content) {
                        return (new JSMinifier($content, true))->min();
                    });
                } elseif (($data = @json_decode($content)) !== null
                         && ($newContent = json_encode(
                             $data,
                             JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                         )) !== false
                ) {
                    $content = str_replace('</', '<\\/', $newContent);
                }
                $element->setTextContent($content);
            }
            yield $element;
        }
    }
}
