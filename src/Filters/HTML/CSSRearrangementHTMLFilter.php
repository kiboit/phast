<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\ValueObjects\URL;

class CSSRearrangementHTMLFilter extends RearrangementHTMLFilter {

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * CSSRearrangementHTMLFilter constructor.
     *
     * @param URL $baseUrl
     */
    public function __construct(URL $baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    protected function getElementsToRearrange(\DOMDocument $document) {
        $links = $document->getElementsByTagName('link');
        foreach ($links as $link) {
            if ($this->isRemoteCSS($link)) {
                yield $link;
            }
        }
    }

    private function isRemoteCSS(\DOMElement $element) {
        return     $element->hasAttribute('rel')
                && $element->getAttribute('rel') == 'stylesheet'
                && $element->hasAttribute('href')
                && !URL::fromString($element->getAttribute('href'))->isLocalTo($this->baseUrl);
    }

}
