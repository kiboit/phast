<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Common\ObjectifiedFunctions;

class CompositeHTMLFilter {

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

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
    public function __construct($maxBufferSizeToApply, ObjectifiedFunctions $functions = null) {
        $this->maxBufferSizeToApply = $maxBufferSizeToApply;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
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

        try {
            $output = $this->tryToApply($buffer, $time_start);
        } catch (\Exception $e) {
            $this->functions->error_log(sprintf(
                'Phast: CompositeHTMLFilter: %s: Msg: %s, Code: %s, File: %s, Line: %s',
                get_class($e),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()

            ));
            $output = $buffer;
        }
        return $output;
    }

    public function addHTMLFilter(HTMLFilter $filter) {
        $this->filters[] = $filter;
    }

    private function tryToApply($buffer, $time_start) {
        $xmlErrors = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8"?' . '>' . $buffer);

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
        $log = "Page automatically optimized by Phast\n\n";
        arsort($timings);
        foreach ($timings as $cls => $time) {
            $cls = preg_replace('~^.*\\\\~', '', $cls);
            $log .= sprintf("      % -43s % 4dms\n", $cls, $time*1000);
            $time_accounted += $time;
        }
        $log .= "\n";
        $log .= sprintf("      % 43s % 4dms\n", '(other)', ($time_delta - $time_accounted)*1000);
        $log .= sprintf("      % 43s % 4dms\n", '(total)', $time_delta*1000);

        $output .= '<script>window.console&&console.log(' . json_encode($log) . ')</script>';

        return $output;
    }

}
