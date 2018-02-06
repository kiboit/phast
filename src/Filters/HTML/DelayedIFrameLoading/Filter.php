<?php

namespace Kibo\Phast\Filters\HTML\DelayedIFrameLoading;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter implements HTMLFilter {
    use LoggingTrait;

    public function transformHTMLDOM(DOMDocument $document) {
        $addScript = false;
        foreach ($document->query('//iframe') as $iframe) {
            /** @var \DOMElement $iframe */
            if (!$iframe->hasAttribute('src')) {
                continue;
            }
            $this->logger()->info('Delaying iframe {src}', ['src' => $iframe->getAttribute('src')]);
            $iframe->setAttribute('data-phast-src', $iframe->getAttribute('src'));
            $iframe->setAttribute('src', 'about:blank');
            $addScript = true;
        }
        if ($addScript) {
            $document->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/iframe-loader.js'));
        }
    }

}
