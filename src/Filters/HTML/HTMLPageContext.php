<?php


namespace Kibo\Phast\Filters\HTML;


use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class HTMLPageContext {

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * @var PhastJavaScript[]
     */
    private $phastJavaScripts = [];

    /**
     * @var \Traversable
     */
    private $elements;

    /**
     * HTMLPageContext constructor.
     * @param URL $baseUrl
     * @param \Traversable $elements
     */
    public function __construct(URL $baseUrl, \Traversable $elements) {
        $this->baseUrl = $baseUrl;
        $this->elements = $elements;
    }


    /**
     * @param URL $baseUrl
     */
    public function setBaseUrl(URL $baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return URL
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * @param PhastJavaScript $script
     */
    public function addPhastJavascript(PhastJavaScript $script) {
        $this->phastJavaScripts[] = $script;
    }

    /**
     * @return PhastJavaScript[]
     */
    public function getPhastJavaScripts() {
        return $this->phastJavaScripts;
    }

    /**
     * @return \Traversable
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * @param \Traversable $elements
     */
    public function setElements(\Traversable $elements) {
        $this->elements = $elements;
    }



}
