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
     * HTMLPageContext constructor.
     * @param URL $baseUrl
     */
    public function __construct(URL $baseUrl) {
        $this->baseUrl = $baseUrl;
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
}
