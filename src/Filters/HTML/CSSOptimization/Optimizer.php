<?php


namespace Kibo\Phast\Filters\HTML\CSSOptimization;


use Kibo\Phast\Common\DOMDocument;

class Optimizer {

    private $classNamePattern = '-?[_a-zA-Z]++[_a-zA-Z0-9-]*+';

    private $usedSelectorPattern;

    public function __construct(DOMDocument $document) {
        $this->usedSelectorPattern = $this->getUsedSelectorPattern($document);
    }

    public function optimizeCSS($css) {
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
            // This is an error condition
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

    private function getUsedSelectorPattern(DOMDocument $document) {
        $classes = $this->getUsedClasses($document);

        $re_class = $classes ? '(?!' . implode('|', $classes) . ')' : '';
        $re_selector = "~\.$re_class{$this->classNamePattern}~";

        return $re_selector;
    }

    private function getUsedClasses(DOMDocument $document) {
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


}
