<?php

namespace Kibo\Phast\Filters;

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

        return $doc->saveHTML();
    }

    public function addHTMLFilter(HTMLFilter $filter) {
        $this->filters[] = $filter;
    }

}
