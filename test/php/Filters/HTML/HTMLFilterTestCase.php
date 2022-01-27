<?php
namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Filters\HTML\PhastScriptsCompiler\PhastJavaScriptCompiler;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class HTMLFilterTestCase extends PhastTestCase {
    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var \DOMElement
     */
    protected $html;

    /**
     * @var \DOMElement
     */
    protected $head;

    /**
     * @var \DOMElement
     */
    protected $body;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $compilerCache;

    /**
     * @var PhastJavaScriptCompiler
     */
    protected $jsCompiler;

    /**
     * @var HTMLStreamFilter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $cspNonce;

    public function setUp(): void {
        parent::setUp();

        $this->dom = new \DOMDocument();
        $this->html = $this->dom->createElement('html');
        $this->head = $this->dom->createElement('head');
        $this->body = $this->dom->createElement('body');
        $this->dom->appendChild($this->html);
        $this->html->appendChild($this->head);
        $this->html->appendChild($this->body);
        $this->cspNonce = null;

        $this->compilerCache = $this->createMock(Cache::class);
        $this->jsCompiler = new PhastJavaScriptCompiler(
            $this->compilerCache,
            '',
            ServiceRequest::FORMAT_PATH
        );
    }

    public function tearDown(): void {
        parent::tearDown();
        ServiceRequest::resetRequestState();
    }

    public function addBaseTag($href) {
        $base = $this->dom->createElement('base');
        $base->setAttribute('href', $href);
        $this->head->appendChild($base);
        return $base;
    }

    protected function applyFilter($inputHtml = null, $skipResultParsing = false) {
        $composite = new Composite\Filter(URL::fromString(self::BASE_URL), true);
        $composite->addHTMLFilter(new BaseURLSetter\Filter());
        $composite->addHTMLFilter($this->filter);
        $composite->addHTMLFilter(
            new PhastScriptsCompiler\Filter($this->jsCompiler, $this->cspNonce)
        );

        $html = is_null($inputHtml) ? $this->dom->saveHTML() : $inputHtml;
        $filtered = $composite->apply($html);

        if (!$skipResultParsing) {
            $this->dom = new \DOMDocument();
            @$this->dom->loadHTML($filtered);
            $this->head = $this->dom->getElementsByTagName('head')->item(0);
            $this->body = $this->dom->getElementsByTagName('body')->item(0);
        }

        return $filtered;
    }

    protected function assertHasCompiled($scriptName) {
        $compiled = $this->getCompiledScript();
        $this->assertNotNull($compiled, 'Failed asserting that phast scripts have been compiled');
        $names = explode(',', $compiled->getAttribute('data-phast-compiled-js-names'));
        $this->assertContains($scriptName, $names, "Failed asserting thata $scriptName has been compiled");
    }

    protected function assertHasNotCompiledScripts() {
        $this->assertNull($this->getCompiledScript(), 'Failed asserting that scripts have not been compiled');
    }

    protected function assertCompiledConfigEquals($expectedConfig, $actualConfigKey) {
        $actualConfig = $this->jsCompiler->getLastCompiledConfig();
        $this->assertObjectHasAttribute($actualConfigKey, $actualConfig, 'Failed asserting that config key exists');
        $this->assertEquals(
            $expectedConfig,
            $actualConfig->$actualConfigKey,
            'Failed asserting that configs are equal'
        );
    }

    /**
     * @return \DOMElement|null
     */
    protected function getCompiledScript() {
        $allScripts = $this->dom->getElementsByTagName('script');
        /** @var \DOMElement[] $compiled */
        $compiled = array_values(array_filter(iterator_to_array($allScripts), function (\DOMElement $element) {
            return $element->hasAttribute('data-phast-compiled-js-names');
        }));
        return empty($compiled) ? null : $compiled[0];
    }

    /**
     * @param \DOMElement $element
     * @param string | null $mark
     * @return string
     */
    protected function markElement(\DOMElement $element, $mark = null) {
        if (is_null($mark)) {
            $mark = uniqid();
        }
        $element->setAttribute('id', $mark);
        return $mark;
    }

    /**
     * @param $tagName
     * @param string|null $mark
     * @return \DOMElement
     */
    protected function makeMarkedElement($tagName, $mark = null) {
        $element = $this->dom->createElement($tagName);
        $this->markElement($element, $mark);
        return $element;
    }

    /**
     * @param \DOMElement $element
     * @return \DOMElement
     */
    protected function getMatchingElement(\DOMElement $element) {
        $this->assertTrue($element->hasAttribute('id'), 'Given element does not have an id');
        $id = $element->getAttribute('id');
        $element = $this->dom->getElementById($id);
        $this->assertNotNull(
            $element,
            "Failed asserting that a matching element exists for id $id"
        );
        return $element;
    }

    protected function assertMatchingElementExists(\DOMElement $element) {
        $this->getMatchingElement($element);
    }

    protected function assertElementsMatch(\DOMElement $expected, \DOMElement $actual) {
        $this->assertTrue($expected->hasAttribute('id'), 'Expected element is not marked');
        $this->assertTrue($actual->hasAttribute('id'), 'Failed asserting elements match. Actual has no mark');
        $this->assertEquals(
            $expected->getAttribute('id'),
            $actual->getAttribute('id'),
            'Failed asserting elements match.'
        );
    }
}
