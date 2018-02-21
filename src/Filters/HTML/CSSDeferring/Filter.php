<?php

namespace Kibo\Phast\Filters\HTML\CSSDeferring;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter extends BaseHTMLStreamFilter {
    use LoggingTrait;

    private $insertLoader = false;

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'link' && $tag->getAttribute('rel') == 'stylesheet';
    }

    protected function handleTag(Tag $link) {
        $this->logger()->info('Deferring {src}', ['src' => $link->getAttribute('href')]);
        $script = new Tag('script', ['type' => 'phast-link']);
        $script->setTextContent(trim($link->toString()));
        $this->insertLoader = true;
        yield $script;
    }

    protected function onBodyEnd() {
        if ($this->insertLoader) {
            $this->logger()->info('Inserting JS loader');
            $this->context->addPhastJavaScript(new PhastJavaScript(__DIR__ . '/styles-loader.js'));
        } else {
            $this->logger()->info('No links were deferred. Not inserting JS loader.');
        }
    }

}
