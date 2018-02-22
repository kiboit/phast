<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Filters\HTML\Composite\Filter;

class OutputBufferHandler {

    private $filter;

    private $buffer = '';

    private $offset = 0;

    private $startPattern = '~
        (
            \s*+ <!doctype\s++html> |
            \s*+ <html> |
            \s*+ <head> |
            \s*+ <!--.*?-->
        )++
    ~xsiA';

    public function __construct(Filter $filter) {
        $this->filter = $filter;
    }

    public function install() {
        $flags =
            PHP_OUTPUT_HANDLER_STDFLAGS &
            ~PHP_OUTPUT_HANDLER_REMOVABLE &
            ~PHP_OUTPUT_HANDLER_CLEANABLE;

        while (@ob_end_flush());
        ob_start([$this, 'handleChunk'], 2, $flags);
        ob_implicit_flush(true);
    }

    public function handleChunk($chunk, $phase) {
        if ($this->buffer === null) {
            return $chunk;
        }
        $this->buffer .= $chunk;
        $output = '';
        if (preg_match($this->startPattern, $this->buffer, $match, 0, $this->offset)) {
            $this->offset += strlen($match[0]);
            $output .= $match[0];
        }
        if ($phase & PHP_OUTPUT_HANDLER_FINAL) {
            $output .= $this->finalize();
        }
        return $output;
    }

    private function finalize() {
        $result = $this->filter->apply($this->buffer, $this->offset);
        $this->buffer = null;
        return $result;
    }

}
