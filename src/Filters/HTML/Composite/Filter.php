<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;

class Filter {
    use LoggingTrait;

    /**
     * @var DOMDocument
     */
    private $dom;

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

    private $timings = [];

    /**
     * Filter constructor.
     * @param $maxBufferSizeToApply
     * @param DOMDocument $dom
     * @param ObjectifiedFunctions|null $functions
     */
    public function __construct($maxBufferSizeToApply, DOMDocument $dom, ObjectifiedFunctions $functions = null) {
        $this->maxBufferSizeToApply = $maxBufferSizeToApply;
        $this->dom = $dom;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    /**
     * @param string $buffer
     * @return string
     */
    public function apply($buffer) {
        $time_start = microtime(true);

        if (strlen($buffer) > $this->maxBufferSizeToApply) {
            $this->logger()->info(
                'Buffer exceeds max. size ({buffersize} bytes). Not applying',
                ['buffersize' => $this->maxBufferSizeToApply]
            );
            return $buffer;
        }


        $pattern = "~
            ^
            \s* (<\?xml[^>]*>)?
            \s* (<!doctype\s+html[^>]*>)?
            (\s* <!--(.*?)-->)*
            \s* <html (?! [^>]* \s ( amp | ⚡ ) [\s=>] )
            .*
            ( </body> | </html> )
        ~isx";

        if (!preg_match($pattern, $buffer)) {
            $this->logger()->info('Buffer doesn\'t look like html! Not applying filters');
            return $buffer;
        }

        try {
            $output = $this->tryToApply($buffer, $time_start);
        } catch (\Exception $e) {
            $this->logger()->critical(
                'Phast: CompositeHTMLFilter: {exception} Msg: {message}, Code: {code}, File: {file}, Line: {line}',
                [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
            $output = $buffer;
        }
        return $output;
    }

    public function addHTMLFilter(HTMLFilter $filter) {
        $this->filters[] = $filter;
    }

    private function tryToApply($buffer, $time_start) {

        $this->time('Parsing', function () use ($buffer) {
            $this->dom->loadHTML($buffer);
        });

        foreach ($this->filters as $filter) {
            $this->logger()->info('Starting {filter}', ['filter' => get_class($filter)]);
            $this->time(get_class($filter), function () use ($filter) {
                $filter->transformHTMLDOM($this->dom);
            });
        }

        $output = $this->time('Serialization', function () {
            return $this->dom->serialize();
        });

        $time_delta = microtime(true) - $time_start;

        $time_accounted = 0.;
        $log = "Page automatically optimized by Phast\n\n";
        arsort($this->timings);
        foreach ($this->timings as $cls => $time) {
            $cls = str_replace('Kibo\Phast\Filters\HTML\\', '', $cls);
            $log .= sprintf("      % -43s % 4dms\n", $cls, $time*1000);
            $time_accounted += $time;
        }
        $log .= "\n";
        $log .= sprintf("      % 43s % 4dms\n", '(other)', ($time_delta - $time_accounted)*1000);
        $log .= sprintf("      % 43s % 4dms\n", '(total)', $time_delta*1000);

        $output .= '<script>window.console&&console.log(' . json_encode($log) . ')</script>';
        $this->logger()->info($log);
        return $output;
    }

    private function time($label, callable $cb) {
        $start = microtime(true);
        $returned = $cb();
        $delta = microtime(true) - $start;
        $this->timings[$label] = $delta;
        $this->logger()->info('Finished {label} in {time}', ['label' => $label, 'time' => $delta]);
        return $returned;
    }

}
