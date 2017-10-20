<?php

namespace Kibo\Phast\Filters;

abstract class HTMLFilter {

    /**
     * @param \DOMDocument $doc
     * @return null
     */
    abstract protected function transformHTML(\DOMDocument $doc);

    /**
     * @param string $buffer
     * @return string
     */
    public function apply($buffer) {
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

        $this->transformHTML($doc);

        libxml_clear_errors();
        libxml_use_internal_errors($xmlErrors);

        return $doc->saveHTML();
    }

}
