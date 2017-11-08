<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\ValueObjects\URL;

class CSSImagesOptimizationServiceHTMLFilter extends ImagesOptimizationServiceHTMLFilter {

    public function transformHTMLDOM(\DOMDocument $document) {
        $styles = $document->getElementsByTagName('style');
        /** @var \DOMElement $style */
        foreach ($styles as $style) {
            $style->textContent = preg_replace_callback(
                '/(\b.*(?:image|background):[^;]*\burl\((?:\'|"|))([^\'")]+)/',
                function ($matches) {
                    $params = ['src' => (string) URL::fromString($matches[2])->withBase($this->baseUrl)];
                    return $matches[1] . $this->makeSignedUrl($this->serviceUrl, $params, $this->signature);
                },
                $style->textContent
            );
        }
    }

}
