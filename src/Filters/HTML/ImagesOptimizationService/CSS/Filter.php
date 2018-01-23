<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags\Filter as TagsFilter;

class Filter extends TagsFilter {

    public function transformHTMLDOM(DOMDocument $document) {
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
                $url = $this->makeURLAbsoluteToBase($matches[2]);
                if ($this->shouldRewriteUrl($url)) {
                    $params = ['src' => $url];
                    return $matches[1] . $this->makeSignedUrl($params);
                }
                return $matches[1] . $matches[2];
            },
            $styleContent
        );
    }

}
