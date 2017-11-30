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
        $utf8Buffer = $this->cleanUTF8($buffer);

        $xmlErrors = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8"?' . '>' . $utf8Buffer);

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

    private function cleanUTF8($buffer) {
        // Treat every byte that is not valid UTF-8 as Windows-1252
        // https://www.w3.org/International/questions/qa-forms-utf-8
        return preg_replace_callback(
            '~
                [\x09\x0A\x0D\x20-\x7E]            # ASCII
              | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
              |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
              | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
              |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
              |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
              | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
              |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
              | (.)
            ~xs',
            function($match) {
                if (isset($match[1]) && strlen($match[1])) {
                    return mb_convert_encoding($match[1], 'UTF-8', 'Windows-1252');
                } else {
                    return $match[0];
                }
            },
            $buffer
        );
    }

}
