<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Parser;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Tokenizer;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;
use Masterminds\HTML5\Parser\Scanner;
use Kibo\Phast\Parsing\HTML\StringInputStream;

class DOMDocument {
    use BodyFinderTrait;

    /**
     * @var HTMLStream
     */
    private $stream;

    /**
     * @var PhastJavaScriptCompiler
     */
    private $jsCompiler;

    /**
     * @var URL
     */
    private $documentLocation;

    /**
     * @var PhastJavaScript[]
     */
    private $phastJavaScripts = [];

    /**
     * DOMDocument constructor.
     * @param HTMLStream $stream
     */
    public function __construct(HTMLStream $stream) {
        $this->stream = $stream;
    }


    /**
     * @param URL $documentLocation
     * @param PhastJavaScriptCompiler $jsCompiler
     * @return DOMDocument
     */
    public static function makeForLocation(URL $documentLocation, PhastJavaScriptCompiler $jsCompiler) {
        $instance = new self(new HTMLStream());
        $instance->documentLocation = $documentLocation;
        $instance->jsCompiler = $jsCompiler;
        return $instance;
    }

    public function setStream(HTMLStream $stream) {
        $this->stream = $stream;
    }

    /**
     * @return HTMLStream
     */
    public function getStream() {
        return $this->stream;
    }

    public function query($query) {
        $tagName = substr($query, 2);
        return $this->getElementsByTagName($tagName);
    }

    /**
     * @param $tagName
     * @return \Kibo\Phast\Parsing\HTML\HTMLStreamElements\TagCollection
     */
    public function getElementsByTagName($tagName) {
        return $this->stream->getElementsByTagName($tagName);
    }

    /**
     * @param $attr
     * @return \Kibo\Phast\Parsing\HTML\HTMLStreamElements\TagCollection
     */
    public function getElementsWithAttr($attr) {
        return $this->stream->getElementsWithAttr($attr);
    }

    public function loadHTML($string) {
        $inputStream = new StringInputStream($string);
        $parser = new Parser($this->stream, $inputStream);
        $tokenizer = new Tokenizer(new Scanner($inputStream), $parser);
        $tokenizer->parse();
    }

    /**
     * @return URL
     */
    public function getBaseURL() {
        $bases = $this->getElementsByTagName('base');
        if ($bases->length > 0) {
            $baseHref = URL::fromString($bases->item(0)->getAttribute('href'));
            return $baseHref->withBase($this->documentLocation);
        }
        return $this->documentLocation;
    }

    /**
     * @param PhastJavaScript $script
     */
    public function addPhastJavaScript(PhastJavaScript $script) {
        $this->phastJavaScripts[] = $script;
    }

    /**
     * @return PhastJavaScript[]
     */
    public function getPhastJavaScripts() {
        return $this->phastJavaScripts;
    }

    public function serializeToHTML5() {
        $this->maybeAddPhastScripts();
        return '';
    }

    public function createElement($tagName) {
        return new Tag($tagName);
    }

    private function maybeAddPhastScripts() {
        if (empty ($this->phastJavaScripts)) {
            return;
        }
        $script = $this->createElement('script');
        $script->textContent = $this->jsCompiler->compileScriptsWithConfig($this->phastJavaScripts);
        $this->getBodyElement($this)->appendChild($script);
    }

}
