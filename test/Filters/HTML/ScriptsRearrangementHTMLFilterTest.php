<?php

namespace Kibo\Phast\Filters\HTML;

use PHPUnit\Framework\TestCase;

class ScriptsRearrangementHTMLFilterTest extends TestCase {

    /**
     * @var ScriptsRearrangementHTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new ScriptsRearrangementHTMLFilter();
    }

    public function testScriptsMoving() {
        $dom = new \DOMDocument();
        $html = $dom->createElement('html');
        $dom->appendChild($html);
        $head = $dom->createElement('head');
        $html->appendChild($head);

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

        $body = $dom->createElement('body');
        $html->appendChild($body);

        $bodyScriptWithTypeJSON = $dom->createElement('script');
        $bodyScriptWithTypeJSON->setAttribute('type', 'application/json');
        $body->appendChild($bodyScriptWithTypeJSON);

        $div = $dom->createElement('div');
        $body->appendChild($div);

        $divScript = $dom->createElement('script');
        $div->appendChild($divScript);

        $this->filter->transformHTMLDOM($dom);

        $this->assertFalse($head->hasChildNodes());
        $this->assertFalse($div->hasChildNodes());

        $this->assertSame($bodyScriptWithTypeJSON, $body->childNodes[0]);
        $this->assertSame($headScriptNoType, $body->childNodes[2]);
        $this->assertSame($headScriptWithTypeText, $body->childNodes[3]);
        $this->assertSame($headScriptWithTypeApp, $body->childNodes[4]);
        $this->assertSame($headScriptWithTypeCharset, $body->childNodes[5]);
        $this->assertSame($divScript, $body->childNodes[6]);
    }

    public function testExceptionOnNoBody() {
        $this->expectException(\Exception::class);
        $dom = new \DOMDocument();
        $this->filter->transformHTMLDOM($dom);
    }

}
