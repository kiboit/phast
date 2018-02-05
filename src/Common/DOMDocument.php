<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class DOMDocument extends \DOMDocument {
    use BodyFinderTrait;

    private $xpath;

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
        $instance->documentLocation = $documentLocation;
        $instance->jsCompiler = $jsCompiler;
        return $instance;
    }

    public function query($query) {
        if (!isset($this->xpath)) {
            $this->xpath = new \DOMXPath($this);
        }
        return $this->xpath->query($query);
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
        // This gets us UTF-8 instead of entities
        $output = '<!doctype html>';
        foreach ($this->childNodes as $node) {
            if (!$node instanceof \DOMDocumentType
                && !$node instanceof \DOMProcessingInstruction
            ) {
                $output .= $this->saveHTML($node);
            }
        }
        return $output;
    }

    private function maybeAddPhastScripts() {
        if (empty ($this->phastJavaScripts)) {
            return;
        }
        $script = $this->createElement('script');
        $script->textContent = $this->jsCompiler->compileScripts($this->phastJavaScripts);
        $this->getBodyElement($this)->appendChild($script);
    }

}
