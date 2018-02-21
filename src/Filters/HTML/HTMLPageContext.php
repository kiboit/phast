<?php


namespace Kibo\Phast\Filters\HTML;


use Kibo\Phast\Common\PhastJavaScriptCompiler;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
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

    /**
     * @param PhastJavaScriptCompiler $jsCompiler
     * @return string
     */
    public function serialize(PhastJavaScriptCompiler $jsCompiler, \Traversable $elements) {
        $scriptsAdded = false;
        $output = '';
        foreach ($elements as $element) {
            if ($this->isClosingBody($element) && $this->shouldAddPhastJS($scriptsAdded)) {
                $output .= $this->getCompiledPhastJS($jsCompiler);
            }
            $output .= $element;
        }
        return $output;
    }

    /**
     * @param $isAdded
     * @return bool
     */
    private function shouldAddPhastJS(&$isAdded) {
        $shouldAdd = !empty ($this->phastJavaScripts) && !$isAdded;
        $isAdded = $shouldAdd;
        return $shouldAdd;
    }

    /**
     * @param Element $element
     * @return bool
     */
    private function isClosingBody(Element $element) {
        return $element instanceof ClosingTag && $element->getTagName() == 'body';
    }

    private function getCompiledPhastJS(PhastJavaScriptCompiler $jsCompiler) {
        return '<script>' . $jsCompiler->compileScriptsWithConfig($this->phastJavaScripts) . '</script>';
    }


}
