<?php

namespace Kibo\Phast\Filters\HTML;

class CompositeHTMLFilter {

    /**
     * @var integer
     */
    private $maxBufferSizeToApply;

    /**
     * @var HTMLFilter[]
     */
    private $filters = [];

    /**
     * CompositeHTMLFilter constructor.
     *
     * @param int $maxBufferSizeToApply
     */
    public function __construct($maxBufferSizeToApply) {
        $this->maxBufferSizeToApply = $maxBufferSizeToApply;
    }

    /**
     * @param string $buffer
     * @return string
     */
    public function apply($buffer) {
        $time_start = microtime(true);

        if (strlen($buffer) > $this->maxBufferSizeToApply) {
            return $buffer;
        }

        $pattern = '~
            ^
            \s* (<\?xml.*>)?
            \s* (<!doctype\s+html.*>)?
            \s* <html
            .*
            ( </body> | </html> )
        ~isx';

        if (!preg_match($pattern, $buffer)) {
            return $buffer;
        }

        $xmlErrors = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8"?>' . $buffer);

        $timings = [];

        foreach ($this->filters as $filter) {
            $time_filter_start = microtime(true);
            $filter->transformHTMLDOM($doc);
            $time_filter_delta = microtime(true) - $time_filter_start;
            $timings[get_class($filter)] = $time_filter_delta;
        }

        libxml_clear_errors();
        libxml_use_internal_errors($xmlErrors);

        // This gets us UTF-8 instead of entities
        $output = '<!doctype html>';
        foreach ($doc->childNodes as $node) {
            if (!$node instanceof \DOMDocumentType
                && !$node instanceof \DOMProcessingInstruction
            ) {
                $output .= $doc->saveHTML($node);
            }
        }

        $time_delta = microtime(true) - $time_start;

        $time_accounted = 0.;
        $output .= "<!--\n    Page optimized by https://kiboit.com/Phast\n";
        arsort($timings);
        foreach ($timings as $cls => $time) {
            $cls = preg_replace('~^.*\\\\~', '', $cls);
            $output .= sprintf("      % -43s %.3fs\n", $cls, $time);
            $time_accounted += $time;
        }
        $output .= sprintf("      % 43s %.3fs\n", '(other)', $time_delta - $time_accounted);
        $output .= sprintf("      % 43s %.3fs\n", '(total)', $time_delta);
        $output .= "-->\n";

        return $output;
    }

    public function addHTMLFilter(HTMLFilter $filter) {
        $this->filters[] = $filter;
    }

}
