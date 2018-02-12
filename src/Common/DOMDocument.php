<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\OpeningTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TextContainingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Parser;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\Tokenizer;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;
use Masterminds\HTML5\Parser\Scanner;
use Masterminds\HTML5\Parser\StringInputStream;

class DOMDocument {

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
     * @param URL $documentLocation
     * @param PhastJavaScriptCompiler $jsCompiler
     * @return DOMDocument
     */
    public static function makeForLocation(URL $documentLocation, PhastJavaScriptCompiler $jsCompiler) {
        $instance = new self();
        $instance->stream = new HTMLStream();
        $instance->documentLocation = $documentLocation;
        $instance->jsCompiler = $jsCompiler;
        return $instance;
    }

    public function query($query) {
        $tagName = substr($query, 2);
        return $this->getElementsByTagName($tagName);
    }

    public function getElementsByTagName($tagName) {
        return $this->stream->getElementsByTagName($tagName);
    }

    public function loadHTML($string) {
        $parser = new Parser($this->stream);
        $inputStream = new StringInputStream($string);
        $tokenizer = new Tokenizer(new Scanner($inputStream), $parser);
        $tokenizer->parse();
    }

    /**
     * @return URL
     */
    public function getBaseURL() {
        $bases = $this->query('//base');
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
        return new TextContainingTag(
            new OpeningTag($tagName, []),
            new ClosingTag($tagName)
        );
    }

    private function maybeAddPhastScripts() {
        if (empty ($this->phastJavaScripts)) {
            return;
        }
        $script = $this->createElement('script');
        $script->textContent = $this->jsCompiler->compileScriptsWithConfig($this->phastJavaScripts);
        $body = $this->getElementsByTagName('body')->item(0);
        $bodyClosing = $this->stream->getClosingTag($body);
        if ($bodyClosing) {
            $bodyClosing->appendChild($script);
        }
    }

}
