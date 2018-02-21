<?php


namespace Kibo\Phast\Filters\HTML\Diagnostics;


use Kibo\Phast\Filters\HTML\Helpers\ElementsToDOMFilterAdapter;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLStreamFilter, HTMLFilter {
    use ElementsToDOMFilterAdapter;

    private $serviceUrl;

    public function __construct($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    public function transformElements(HTMLPageContext $context, \Traversable $elements) {
        $url = (new ServiceRequest())->withUrl(URL::fromString($this->serviceUrl))->serialize();
        $script = new PhastJavaScript(__DIR__ . '/diagnostics.js');
        $script->setConfig('diagnostics', ['serviceUrl' => $url]);
        $context->addPhastJavaScript($script);
    }


}
