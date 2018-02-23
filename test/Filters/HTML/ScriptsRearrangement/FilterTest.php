<?php

namespace Kibo\Phast\Filters\HTML\ScriptsRearrangement;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {

    /**
     * @var Filter
     */
    protected  $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter();
    }

    public function testScriptsMoving() {
        $head = $this->head;
        $body = $this->body;

        $headScriptNoType = $this->makeMarkedElement('script', 'head-no-type');
        $head->appendChild($headScriptNoType);

        $headScriptWithTypeText = $this->makeMarkedElement('script', 'head-type-text');
        $headScriptWithTypeText->setAttribute('type', 'text/javascript');
        $head->appendChild($headScriptWithTypeText);

        $headScriptWithTypeApp = $this->makeMarkedElement('script', 'head-type-app');
        $headScriptWithTypeApp->setAttribute('type', 'application/javascript');
        $head->appendChild($headScriptWithTypeApp);

        $headScriptWithTypeCharset = $this->makeMarkedElement('script', 'head-type-charset');
        $headScriptWithTypeCharset->setAttribute('type', 'text/javascript; charset="UTF-8"');
        $head->appendChild($headScriptWithTypeCharset);

        $bodyScriptWithTypeJSON = $this->makeMarkedElement('script', 'head-type-json');
        $bodyScriptWithTypeJSON->setAttribute('type', 'application/json');
        $body->appendChild($bodyScriptWithTypeJSON);

        $div = $this->makeMarkedElement('div', 'div');
        $body->appendChild($div);

        $divScript = $this->makeMarkedElement('script', 'div-script');
        $body->appendChild($divScript);


        $this->applyFilter();

        $this->assertEquals(0, $this->head->childNodes->length);

        $elements = $this->body->childNodes;

        $this->assertElementsMatch($bodyScriptWithTypeJSON, $elements->item(0));
        $this->assertElementsMatch($div, $elements->item(1));
        $this->assertElementsMatch($headScriptNoType, $elements->item(2));
        $this->assertElementsMatch($headScriptWithTypeText, $elements->item(3));
        $this->assertElementsMatch($headScriptWithTypeApp, $elements->item(4));
        $this->assertElementsMatch($headScriptWithTypeCharset, $elements->item(5));
        $this->assertElementsMatch($divScript, $elements->item(6));

    }

    public function testDoubleBodyClosingTag() {
        $html = '<html><head><script>the-script</script></head><body></body></body></html>';
        $actual = $this->applyFilter($html, true);

        $expected = '<html><head></head><body><script>the-script</script></body></body></html>';
        $this->assertStringStartsWith($expected, $actual);
    }



}
