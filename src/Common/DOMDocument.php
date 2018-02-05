<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class DOMDocument extends \DOMDocument {

    private $xpath;

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
     * @return DOMDocument
     */
    public static function makeForLocation(URL $documentLocation) {
        $instance = new self();
        $instance->documentLocation = $documentLocation;
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

}
