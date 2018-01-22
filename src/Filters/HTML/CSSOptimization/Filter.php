<?php

namespace Kibo\Phast\Filters\HTML\CSSOptimization;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;

class Filter implements HTMLFilter {
    use BodyFinderTrait;

    private $loaderScript = <<<EOS
(function() {
    Array.prototype.forEach.call(
        document.querySelectorAll('script[data-phast-css-ref]'),
        restoreStyle
    );

    function restoreStyle(style) {
        var ref = style.getAttribute('data-phast-css-ref');
        var replace = document.querySelector('style[data-phast-css="' + ref + '"]');

        if (replace) {
            replace.textContent = style.textContent;
        }
    };
})();
EOS;

    public function transformHTMLDOM(DOMDocument $document) {
        $body = $this->getBodyElement($document);
        $styles = iterator_to_array($document->query('//style'));

        $optimizer = new Optimizer($document);

        $i = 0;

        foreach ($styles as $style) {
            if (!$this->isStyle($style)) {
                continue;
            }

            $optimized_css = $optimizer->optimizeCSS($style->textContent);

            if ($optimized_css === null) {
                continue;
            }

            $script = $document->createElement('script');
            $script->textContent = $style->textContent;
            $script->setAttribute('type', 'phast-css');
            $script->setAttribute('data-phast-css-ref', ++$i);

            $style->textContent = $optimized_css;
            $style->setAttribute('data-phast-css', $i);

            $body->appendChild($script);
        }

        if ($i > 0) {
            $script = $document->createElement('script');
            $script->textContent = $this->loaderScript;
            $script->setAttribute('async', '');
            $script->setAttribute('data-phast-no-defer', '');
            $body->appendChild($script);
        }
    }

    private function isStyle(\DOMElement $style) {
        $type = $style->getAttribute('type');

        if ($type != '' && $type != 'text/css') {
            return false;
        }

        return true;
    }
}
