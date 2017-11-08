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
        $doc->loadHTML($buffer);

        foreach ($this->filters as $filter) {
            $filter->transformHTMLDOM($doc);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($xmlErrors);

        $output = $doc->saveHTML();

        $time_delta = microtime(true) - $time_start;

        $output .= "<!-- Page optimized by https://kiboit.com/Phast in " .
                   number_format($time_delta, 3, '.', '') . "s -->\n";

        return $output;
    }

    public function addHTMLFilter(HTMLFilter $filter) {
        $this->filters[] = $filter;
    }

}
