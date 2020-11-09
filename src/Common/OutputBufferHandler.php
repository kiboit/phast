<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Logging\LoggingTrait;

class OutputBufferHandler {
    use LoggingTrait;

    const START_PATTERN = '~
        (
            \s*+ <!doctype\s++html\b[^<>]*> |
            \s*+ <html\b[^<>]*> |
            \s*+ <head> |
            \s*+ <!--.*?-->
        )++
    ~xsiA';

    private $filterCb;

    /**
     * @var ?string
     */
    private $buffer = '';

    private $offset = 0;

    /**
     * @var integer
     */
    private $maxBufferSizeToApply;

    private $canceled = false;

    public function __construct($maxBufferSizeToApply, callable $filterCb) {
        $this->maxBufferSizeToApply = $maxBufferSizeToApply;
        $this->filterCb = $filterCb;
    }

    public function install() {
        $ignoreHandlers = ['default output handler', 'ob_gzhandler'];
        if (!array_diff(ob_list_handlers(), $ignoreHandlers)) {
            while (ob_get_level() && @ob_end_clean());
        }
        ob_start([$this, 'handleChunk'], 2);
        ob_implicit_flush(1);
    }

    public function handleChunk($chunk, $phase) {
        if ($this->buffer === null) {
            return $chunk;
        }

        $this->buffer .= $chunk;

        if ($this->canceled) {
            return $this->stop();
        }

        if (strlen($this->buffer) > $this->maxBufferSizeToApply) {
            $this->logger()->info(
                'Buffer exceeds max. size ({buffersize} bytes). Not applying',
                ['buffersize' => $this->maxBufferSizeToApply]
            );
            return $this->stop();
        }

        $output = '';

        if (preg_match(self::START_PATTERN, $this->buffer, $match, 0, $this->offset)) {
            $this->offset += strlen($match[0]);
            $output .= $match[0];
        }

        if ($phase & PHP_OUTPUT_HANDLER_FINAL) {
            $output .= $this->finalize();
        }

        if ($output !== '') {
            @header_remove('Content-Length');
        }

        return $output;
    }

    private function finalize() {
        $input = substr($this->buffer, $this->offset);
        $result = call_user_func($this->filterCb, $input, $this->buffer);
        $this->buffer = null;
        return $result;
    }

    private function stop() {
        $output = $this->buffer;
        $this->buffer = null;
        return $output;
    }

    public function cancel() {
        $this->canceled = true;
    }
}
