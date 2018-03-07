<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Filters\HTML\Composite\Filter;
use Kibo\Phast\Logging\LoggingTrait;

class OutputBufferHandler {
    use LoggingTrait;

    const START_PATTERN = '~
        (
            \s*+ <!doctype\s++html> |
            \s*+ <html> |
            \s*+ <head> |
            \s*+ <!--.*?-->
        )++
    ~xsiA';

    CONST DOCUMENT_PATTERN = "~
        \s* (<\?xml[^>]*>)?
        \s* (<!doctype\s+html[^>]*>)?
        (\s* <!--(.*?)-->)*
        \s* <html (?! [^>]* \s ( amp | ⚡ ) [\s=>] )
        .*
        ( </body> | </html> )
    ~xsiA";

    private $filter;

    private $buffer = '';

    private $offset = 0;

    /**
     * @var integer
     */
    private $maxBufferSizeToApply;

    public function __construct($maxBufferSizeToApply, Filter $filter) {
        $this->maxBufferSizeToApply = $maxBufferSizeToApply;
        $this->filter = $filter;
    }

    public function install() {
        while (@ob_end_flush());
        ob_start([$this, 'handleChunk'], 2);
        ob_implicit_flush(true);
    }

    public function handleChunk($chunk, $phase) {
        if ($this->buffer === null) {
            return $chunk;
        }
        $this->buffer .= $chunk;
        if (strlen($this->buffer) > $this->maxBufferSizeToApply) {
            $this->logger()->info(
                'Buffer exceeds max. size ({buffersize} bytes). Not applying',
                ['buffersize' => $this->maxBufferSizeToApply]
            );
            $output = $this->buffer;
            $this->buffer = null;
            return $output;
        }

        $output = '';
        if (preg_match(self::START_PATTERN, $this->buffer, $match, 0, $this->offset)) {
            $this->offset += strlen($match[0]);
            $output .= $match[0];
        }
        if ($phase & PHP_OUTPUT_HANDLER_FINAL) {
            $output .= $this->finalize();
        }
        return $output;
    }

    private function finalize() {
        $input = substr($this->buffer, $this->offset);

        if (!preg_match(self::DOCUMENT_PATTERN, $this->buffer)) {
            $this->logger()->info('Buffer doesn\'t look like html! Not applying filters');
            return $input;
        }

        $this->buffer = null;
        $result = $this->filter->apply($input);

        return $result;
    }

}
