<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\ValueObjects\URL;

class CSSImagesOptimizationServiceHTMLFilter extends ImagesOptimizationServiceHTMLFilter {

    public function transformHTMLDOM(\DOMDocument $document) {
        $styleTags = $document->getElementsByTagName('style');
        /** @var \DOMElement $styleTag */
        foreach ($styleTags as $styleTag) {
            $styleTag->textContent = $this->rewriteStyle($styleTag->textContent);
        }

        $styleAttrs = (new \DOMXPath($document))->query('//@style');
        /** @var \DOMAttr $styleAttr */
        foreach ($styleAttrs as $styleAttr) {
            $styleAttr->value = htmlspecialchars($this->rewriteStyle($styleAttr->value));
        }
    }

    private function rewriteStyle($styleContent) {
        return preg_replace_callback(
            '/(\b.*(?:image|background):[^;]*\burl\((?:\'|"|))([^\'")]+)/',
            function ($matches) {
                $params = ['src' => (string) URL::fromString($matches[2])->withBase($this->baseUrl)];
                return $matches[1] . $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
            },
            $styleContent
        );
    }

}
