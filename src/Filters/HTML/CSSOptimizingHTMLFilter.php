<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;

class CSSOptimizingHTMLFilter implements HTMLFilter {
    use BodyFinderTrait;

    private $classNamePattern = '-?[_a-zA-Z]++[_a-zA-Z0-9-]*+';

    private $usedSelectorPattern;

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

    public function transformHTMLDOM(\Kibo\Phast\Common\DOMDocument $document) {
        $body = $this->getBodyElement($document);
        $styles = iterator_to_array($document->getElementsByTagName('style'));

        $this->usedSelectorPattern = $this->getUsedSelectorPattern($document);

        $i = 0;

        foreach ($styles as $style) {
            if (!$this->isStyle($style)) {
                continue;
            }

            $optimized_css = $this->optimizeCSS($style->textContent);

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

    private function getUsedSelectorPattern(\Kibo\Phast\Common\DOMDocument $document) {
        $classes = $this->getUsedClasses($document);

        $re_class = $classes ? '(?!' . implode('|', $classes) . ')' : '';
        $re_selector = "~\.$re_class{$this->classNamePattern}~";

        return $re_selector;
    }

    private function getUsedClasses(\Kibo\Phast\Common\DOMDocument $document) {
        $classes = [];

        foreach ($document->query('//@class') as $class) {
            foreach (preg_split('/\s+/', $class->value) as $cls) {
                if ($cls != ''
                    && !isset($classes[$cls])
                    && preg_match("/^{$this->classNamePattern}$/", $cls)
                ) {
                    $classes[$cls] = true;
                }
            }
        }

        return array_keys($classes);
    }

    private function isStyle(\DOMElement $style) {
        $type = $style->getAttribute('type');

        if ($type != '' && $type != 'text/css') {
            return false;
        }

        return true;
    }

    private function optimizeCSS($css) {
        $re_simple_selector_chars = "[A-Z0-9_.#*:()>+\~\s-]";
        $re_selector = "(?: $re_simple_selector_chars | \[[a-z]++\] )++";
        $re_rule = "~
            (?<= ^ | [;}] ) \s*+
            ( (?: $re_selector , )*+ $re_selector )
            ( { [^}]*+ } )
        ~xi";

        $css = preg_replace_callback(
            $re_rule,
            function ($match) {
                return $this->optimizeRule($match[1], $match[2]);
            },
            $css
        );

        if ($css === null) {
            return;
        }

        return trim($css);
    }

    private function optimizeRule($selectors, $body) {
        $new_selectors = [];

        foreach (explode(',', $selectors) as $selector) {
            if ($this->selectorCouldMatch($selector)) {
                $new_selectors[] = $selector;
            }
        }

        if ($new_selectors) {
            return implode(',', $new_selectors) . $body;
        }

        return '';
    }

    private function selectorCouldMatch($selector) {
        return !preg_match($this->usedSelectorPattern, $selector);
    }

}
