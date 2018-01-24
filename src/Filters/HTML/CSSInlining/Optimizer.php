<?php


namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Common\DOMDocument;

class Optimizer {

    private $classNamePattern = '-?[_a-zA-Z]++[_a-zA-Z0-9-]*+';

    private $usedClasses;

    public function __construct(DOMDocument $document) {
        $this->usedClasses = $this->getUsedClasses($document);
    }

    public function optimizeCSS($css) {
        // TODO: This operation may be cached.
        $stylesheet = $this->parseCSS($css);

        if ($stylesheet === null) {
            return;
        }

        $output = '';

        foreach ($stylesheet as $rule) {
            if (isset($rule[1])) {
                $output .= $this->optimizeRule($rule[0], $rule[1]);
            } else {
                $output .= $rule[0];
            }
        }

        return trim($output);
    }

    private function optimizeRule(array $selectors, $body) {
        $new_selectors = [];

        foreach ($selectors as $classes) {
            foreach ($classes as $i => $class) {
                if ($i != 0 && !isset($this->usedClasses[$class])) {
                    break 2;
                }
            }

            $new_selectors[] = $classes[0];
        }

        if ($new_selectors) {
            return implode(',', $new_selectors) . $body;
        }

        return '';
    }

    /**
     * Parse a stylesheet into an array of segments
     *
     * A segment with one element is a unprocessed piece of CSS that will always
     * be output. Offset 0 will contain the contents of this piece.
     *
     * A segment with two elements is a processed selector with a
     * body that can potentially be optimized. Offset 0 will contain the pre-
     * processed selectors (see parseSelectors), and offset 1 will contain the
     * body of this rule.
     *
     * @param $css
     * @return array|void
     */
    public function parseCSS($css) {
        $re_simple_selector_chars = "[A-Z0-9_.#*:()>+\~\s-]";
        $re_selector = "(?: $re_simple_selector_chars | \[[a-z]++\] )++";
        $re_rule = "~
            (?<= ^ | [;}] ) \s*+
            ( (?: $re_selector , )*+ $re_selector )
            ( { [^}]*+ } )
        ~xi";

        if (preg_match_all($re_rule, $css, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) === false) {
            // This is an error condition
            return;
        }

        $offset = 0;
        $stylesheet = [];

        foreach ($matches as $match) {
            if ($match[0][1] > $offset) {
                $stylesheet[] = [substr($css, $offset, $match[0][1] - $offset)];
            }
            $stylesheet[] = [$this->parseSelectors($match[1][0]), $match[2][0]];
            $offset = $match[0][1] + strlen($match[0][0]);
        }

        if ($offset < strlen($css)) {
            $stylesheet[] = [substr($css, $offset)];
        }

        return $stylesheet;
    }

    /**
     * Parse the selector part of a CSS rule into an array of selectors.
     *
     * Each selector will be an array with at offset 0, the string contents of
     * the selector. The rest of the array will be the class names (if any) that
     * must be present in the document for this selector to match.
     *
     * @param string $selectors
     * @return array
     */
    private function parseSelectors($selectors) {
        $new_selectors = [];

        foreach (explode(',', $selectors) as $selector) {
            $classes = [$selector];
            if (preg_match_all("~\.({$this->classNamePattern})~", $selector, $matches)) {
                foreach ($matches[1] as $class) {
                    $classes[] = $class;
                }
            }
            $new_selectors[] = $classes;
        }

        return $new_selectors;
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

        return $classes;
    }

}
