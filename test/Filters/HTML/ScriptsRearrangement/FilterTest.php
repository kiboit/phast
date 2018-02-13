<?php

namespace Kibo\Phast\Filters\HTML\ScriptsRearrangement;

use Kibo\Phast\Filters\HTML\RearrangementHTMLFilterTestCase;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;

class FilterTest extends RearrangementHTMLFilterTestCase {

    /**
     * @var Filter
     */
    protected  $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testScriptsMoving() {
        $dom = $this->dom;
        $head = $this->head;
        $body = $this->body;

        $headScriptNoType = $dom->createElement('script');
        $head->appendChild($headScriptNoType);

        $headScriptWithTypeText = $dom->createElement('script');
        $headScriptWithTypeText->setAttribute('type', 'text/javascript');
        $head->appendChild($headScriptWithTypeText);

        $headScriptWithTypeApp = $dom->createElement('script');
        $headScriptWithTypeApp->setAttribute('type', 'application/javascript');
        $head->appendChild($headScriptWithTypeApp);

        $headScriptWithTypeCharset = $dom->createElement('script');
        $headScriptWithTypeCharset->setAttribute('type', 'text/javascript; charset="UTF-8"');
        $head->appendChild($headScriptWithTypeCharset);

        $bodyScriptWithTypeJSON = $dom->createElement('script');
        $bodyScriptWithTypeJSON->setAttribute('type', 'application/json');
        $body->appendChild($bodyScriptWithTypeJSON);

        $div = $dom->createElement('div');
        $body->appendChild($div);

        $divScript = $dom->createElement('script');
        $body->appendChild($divScript);

        $closingDiv = new ClosingTag('div');
        $body->appendChild($closingDiv);

        $this->filter->transformHTMLDOM($dom);

        $this->assertEquals(2, $this->stream->getElementIndex($this->head));

        $openingDivIndex = $this->stream->getElementIndex($div);
        $this->assertEquals($openingDivIndex + 1, $this->stream->getElementIndex($closingDiv));

        $this->assertEquals(4, $this->stream->getElementIndex($bodyScriptWithTypeJSON));
        $this->assertEquals(7, $this->stream->getElementIndex($headScriptNoType));
        $this->assertEquals(8, $this->stream->getElementIndex($headScriptWithTypeText));
        $this->assertEquals(9, $this->stream->getElementIndex($headScriptWithTypeApp));
        $this->assertEquals(10, $this->stream->getElementIndex($headScriptWithTypeCharset));
        $this->assertEquals(11, $this->stream->getElementIndex($divScript));
    }
}
