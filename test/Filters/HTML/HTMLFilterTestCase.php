<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Common\PhastJavaScriptCompiler;
use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class HTMLFilterTestCase extends TestCase {

    const BASE_URL = 'http://kibo-test.org';

    /**
     * @var HTMLStream
     */
    protected $stream;

    /**
     * @var \Kibo\Phast\Common\DOMDocument
     */
    protected $dom;

    /**
     * @var Tag
     */
    protected $openingHtml;

    /**
     * @var ClosingTag
     */
    protected $html;

    /**
     * @var Tag
     */
    protected $openingHead;

    /**
     * @var ClosingTag
     */
    protected $head;

    /**
     * @var Tag
     */
    protected $openingBody;

    /**
     * @var ClosingTag
     */
    protected $body;

    public function setUp() {
        parent::setUp();

        $this->stream = new HTMLStream();

        $jsCompiler = $this->createMock(PhastJavaScriptCompiler::class);
        $this->dom = DOMDocument::makeForLocation(
            URL::fromString(self::BASE_URL),
            $jsCompiler
        );
        $this->dom->setStream($this->stream);

        $this->openingHtml = new Tag('html');
        $this->openingHead = new Tag('head');
        $this->head = new ClosingTag('head');
        $this->openingBody = new Tag('body');
        $this->body = new ClosingTag('body');
        $this->html = new ClosingTag('html');


        $this->stream->addElement($this->openingHtml);
        $this->stream->addElement($this->openingHead);
        $this->stream->addElement($this->head);
        $this->stream->addElement($this->openingBody);
        $this->stream->addElement($this->body);
        $this->stream->addElement($this->html);

    }

    public function addBaseTag($href) {
        $base = $this->dom->createElement('base');
        $base->setAttribute('href', $href);
        $this->head->appendChild($base);
        return $base;
    }

    protected function getHeadElements() {
        return $this->stream->getElementsBetween($this->openingHead, $this->head);
    }

    protected function getBodyElements() {
        return $this->stream->getElementsBetween($this->openingBody, $this->body);
    }

}
