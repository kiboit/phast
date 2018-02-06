<?php


namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;


use Kibo\Phast\ValueObjects\PhastJavaScript;

class RewriteFunctionPhastJavaScript extends PhastJavaScript {

    /**
     * @var array
     */
    private $config = [];

    /**
     * @param array $config
     */
    public function setConfig(array $config) {
        $this->config = $config;
    }

    public function getContents() {
        return preg_replace('/;?$/', '(' . json_encode($this->config) . ');', parent::getContents());
    }

}
