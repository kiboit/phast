<?php


namespace Kibo\Phast\Filters\HTML\Diagnostics;


use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLFilter {

    private $serviceUrl;

    public function __construct($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $url = (new ServiceRequest())->withUrl(URL::fromString($this->serviceUrl))->serialize();
        $script = new DiagnosticsPhastJavaScript(__DIR__ . '/diagnostics.js');
        $script->setServiceUrl($url);
        $document->addPhastJavaScript($script);
    }


}
