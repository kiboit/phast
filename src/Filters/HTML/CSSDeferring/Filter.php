<?php

namespace Kibo\Phast\Filters\HTML\CSSDeferring;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter implements HTMLFilter {
    use LoggingTrait;

    public function transformHTMLDOM(DOMDocument $document) {
        $insert_loader = false;

        /** @var \DOMElement $link */
        foreach (iterator_to_array($document->query('//link')) as $link) {
            if ($link->getAttribute('rel') != 'stylesheet') {
                continue;
            }

            $this->logger()->info('Deferring {src}', ['src' => $link->getAttribute('href')]);
            $script = $document->createElement('script', trim($document->saveHTML($link)));
            $script->setAttribute('type', 'phast-link');

            $link->parentNode->insertBefore($script, $link);
            $link->parentNode->removeChild($link);

            $insert_loader = true;
        }

        if ($insert_loader) {
            $this->logger()->info('Inserting JS loader');
            $document->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/styles-loader.js'));
        } else {
            $this->logger()->info('No links were deferred. Not inserting JS loader.');
        }
    }

}
