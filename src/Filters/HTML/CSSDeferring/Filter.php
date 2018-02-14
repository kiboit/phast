<?php

namespace Kibo\Phast\Filters\HTML\CSSDeferring;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter implements HTMLFilter {
    use LoggingTrait;

    public function transformHTMLDOM(DOMDocument $document) {
        $insert_loader = false;

        /** @var Tag $link */
        foreach (iterator_to_array($document->getElementsByTagName('link')) as $link) {
            if ($link->getAttribute('rel') != 'stylesheet') {
                continue;
            }

            $this->logger()->info('Deferring {src}', ['src' => $link->getAttribute('href')]);
            $script = $document->createElement('script');
            $script->setTextContent(trim($link->toString()));
            $script->setAttribute('type', 'phast-link');

            $stream = $document->getStream();
            $stream->insertBeforeElement($link, $script);
            $stream->removeElement($link);

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
