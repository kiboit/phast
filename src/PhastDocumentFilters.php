<?php

namespace Kibo\Phast;

use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Filters\HTML\Composite\Factory;
use Kibo\Phast\Filters\HTML\Composite\Filter;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Services\ServiceRequest;

class PhastDocumentFilters {

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

    public static function deploy(array $userConfig) {
        $request = ServiceRequest::fromHTTPRequest(Request::fromGlobals());
        $runtimeConfig = Configuration::fromDefaults()
            ->withUserConfiguration(new Configuration($userConfig))
            ->withServiceRequest($request)
            ->getRuntimeConfig()
            ->toArray();
        Log::init($runtimeConfig['logging'], $request, 'dom-filters');
        if (!$runtimeConfig['switches']['phast']) {
            Log::info('Phast is off. Skipping document filter deployment!');
            return;
        }
        $filter = (new Factory())->make($runtimeConfig);
        Log::info('Phast deployed!');
        $handler = new self($filter);
        $handler->startBuffer();
    }

    public function __construct(Filter $filter) {
        $this->filter = $filter;
    }

    private function startBuffer() {
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
        if (preg_match($this->startPattern, $this->buffer, $match, $this->offset)) {
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
