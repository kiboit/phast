<?php


namespace Kibo\Phast\Filters\HTML\Diagnostics;


use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class DiagnosticsPhastJavaScript extends PhastJavaScript {

    /**
     * @var string
     */
    private $serviceUrl = '';

    /**
     * @param string $serviceUrl
     */
    public function setServiceUrl($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * @return bool|string
     */
    public function getContents() {
        return sprintf(parent::getContents(), $this->serviceUrl);
    }
}
