<?php


namespace Kibo\Phast\Filters\HTML\Diagnostics;

use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\PhastJavaScript;
use Kibo\Phast\ValueObjects\URL;

class Filter implements HTMLStreamFilter {
    private $serviceUrl;

    public function __construct($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $url = (new ServiceRequest())->withUrl(URL::fromString($this->serviceUrl))->serialize();
        $script = PhastJavaScript::fromFile(__DIR__ . '/diagnostics.js');
        $script->setConfig('diagnostics', ['serviceUrl' => $url]);
        $context->addPhastJavaScript($script);
        foreach ($elements as $element) {
            yield $element;
        }
    }
}
