<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags\Filter as TagsFilter;
use Kibo\Phast\ValueObjects\URL;

class Filter extends TagsFilter {

    public function transformHTMLDOM(\Kibo\Phast\Common\DOMDocument $document) {
        $styleTags = $document->query('//style');
        /** @var \DOMElement $styleTag */
        foreach ($styleTags as $styleTag) {
            $styleTag->textContent = $this->rewriteStyle($styleTag->textContent);
        }

        $styleAttrs = $document->query('//@style');
        /** @var \DOMAttr $styleAttr */
        foreach ($styleAttrs as $styleAttr) {
            $styleAttr->value = htmlspecialchars($this->rewriteStyle($styleAttr->value));
        }
    }

    private function rewriteStyle($styleContent) {
        return preg_replace_callback(
            '~
                (
                    \b (?: image | background ):
                    [^;}]*
                    \b url \( [\'"]?
                )
                (
                    [^\'")] ++
                )
            ~xiS',
            function ($matches) {
                if ($this->shouldRewriteUrl($matches[2])) {
                    $params = ['src' => (string) URL::fromString($matches[2])->withBase($this->baseUrl)];
                    return $matches[1] . $this->makeSignedUrl($params);
                }
                return $matches[1] . $matches[2];
            },
            $styleContent
        );
    }

}
