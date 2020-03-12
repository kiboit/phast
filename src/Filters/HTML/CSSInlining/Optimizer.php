<?php


namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class Optimizer {
    private $classNamePattern = '-?[_a-zA-Z]++[_a-zA-Z0-9-]*+';

    /**
     * @var array
     */
    private $usedClasses;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(\Traversable $elements, Cache $cache) {
        $this->usedClasses = $this->getUsedClasses($elements);
        $this->cache = $cache;
    }

    public function optimizeCSS($css) {
        $stylesheet = $this->cache->get(md5($css), function () use ($css) {
            return $this->parseCSS($css);
        });

        if ($stylesheet === null) {
            return;
        }

        $output = '';
        $selectors = null;

        foreach ($stylesheet as $element) {
            if (is_array($element)) {
                if ($selectors === null) {
                    $selectors = [];
                }
                foreach ($element as $i => $class) {
                    if ($i !== 0 && !isset($this->usedClasses[$class])) {
                        continue 2;
                    }
                }
                $selectors[] = $element[0];
            } elseif ($selectors !== null) {
                if (isset($selectors[0])) {
                    $output .= implode(',', $selectors) . $element;
                }
                $selectors = null;
            } else {
                $output .= $element;
            }
        }

        $output = $this->removeEmptyMediaQueries($output);

        return trim($output);
    }

    /**
     * Parse a stylesheet into an array of segments
     *
     * Each string segment is preceded by zero or more arrays encoding selectors
     * parsed by parseSelector (see below).
     *
     * @param $css
     * @return array|void
     */
    private function parseCSS($css) {
        $re_simple_selector_chars = "[A-Z0-9_.#*:>+\~\s-]";
        $re_selector = "(?: $re_simple_selector_chars | \[[a-z]++\] )++";
        $re_rule = "~
            (?<= ^ | [;{}] ) \s*+
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
            $selectors = $this->parseSelectors($match[1][0]);
            if ($selectors === null) {
                continue;
            }
            if ($match[0][1] > $offset) {
                $stylesheet[] = substr($css, $offset, $match[0][1] - $offset);
            }
            foreach ($selectors as $selector) {
                $stylesheet[] = $selector;
            }
            $stylesheet[] = $match[2][0];
            $offset = $match[0][1] + strlen($match[0][0]);
        }

        if ($offset < strlen($css)) {
            $stylesheet[] = substr($css, $offset);
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
     * Null is returned if none of the selectors use classes, and can therefore
     * not be optimized.
     *
     * @param string $selectors
     * @return array|void
     */
    private function parseSelectors($selectors) {
        $newSelectors = [];
        $anyClasses = false;

        foreach (explode(',', $selectors) as $selector) {
            $classes = [$selector];
            if (preg_match_all("~\.({$this->classNamePattern})~", $selector, $matches)) {
                foreach ($matches[1] as $class) {
                    $classes[] = $class;
                    $anyClasses = true;
                }
            }
            $newSelectors[] = $classes;
        }

        if (!$anyClasses) {
            return;
        }

        return $newSelectors;
    }

    private function getUsedClasses(\Traversable $elements) {
        $classes = [];

        /** @var Tag $tag */
        foreach ($elements as $tag) {
            if (!($tag instanceof Tag)) {
                continue;
            }
            foreach (preg_split('/\s+/', $tag->getAttribute('class')) as $cls) {
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

    private function removeEmptyMediaQueries($css) {
        return preg_replace('~@media\s++[A-Z0-9():,\s-]++\s*+{}~i', '', $css);
    }
}
