<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\ValueObjects\URL;

class CSSImagesOptimizationServiceHTMLFilter extends ImagesOptimizationServiceHTMLFilter {

    public function transformHTMLDOM(\Kibo\Phast\Common\DOMDocument $document) {
        $styleTags = $document->getElementsByTagName('style');
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
                    [^\'")] +
                )
            ~xi',
            function ($matches) {
                if ($this->shouldRewriteUrl($matches[2])) {
                    $params = ['src' => (string) URL::fromString($matches[2])->withBase($this->baseUrl)];
                    return $matches[1] . $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
                }
                return $matches[1] . $matches[2];
            },
            $styleContent
        );
    }

}
