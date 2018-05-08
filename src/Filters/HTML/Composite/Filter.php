<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Common\JSON;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Parsing\HTML\PCRETokenizer;
use Kibo\Phast\ValueObjects\URL;

class Filter {
    use LoggingTrait;

    /**
     * @var URL
     */
    private $baseUrl;

    /**
     * @var HTMLStreamFilter[]
     */
    private $filters = [];

    private $timings = [];

    /**
     * Filter constructor.
     * @param URL $baseUrl
     */
    public function __construct(URL $baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $buffer
     * @return string
     */
    public function apply($buffer) {
        $timeStart = microtime(true);

        try {
            return $this->tryToApply($buffer, $timeStart);
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
            return $buffer;
        }
    }

    public function addHTMLFilter(HTMLStreamFilter $filter) {
        $this->filters[] = $filter;
    }

    private function tryToApply($buffer, $timeStart) {

        $context = new HTMLPageContext($this->baseUrl);
        $elements = (new PCRETokenizer())->tokenize($buffer);

        foreach ($this->filters as $filter) {
            $this->logger()->info('Starting {filter}', ['filter' => get_class($filter)]);
            $this->time(get_class($filter), function () use ($filter, $context, &$elements) {
                $elements = $filter->transformElements($elements, $context);
            });
        }

        $output = $this->time('Serialization', function () use ($elements) {
            $output = '';
            foreach ($elements as $element) {
                $output .= $element;
            }
            return $output;
        });

        $timeDelta = microtime(true) - $timeStart;

        $msTimings = array_map(function ($t) { return round($t * 1000); }, $this->timings);
        arsort($msTimings);

        $timeAccounted = 0.;
        $log = '';
        foreach ($msTimings as $cls => $time) {
            $cls = str_replace('Kibo\Phast\Filters\HTML\\', '', $cls);
            $log .= sprintf("% -43s % 4dms\n", $cls, $time);
            $timeAccounted += $time / 1000;
        }
        $log .= "\n";
        $log .= sprintf("% 43s % 4dms\n", '(other)', ($timeDelta - $timeAccounted)*1000);
        $log .= sprintf("% 43s % 4dms\n", '(total)', $timeDelta*1000);

        $output .= "<!--[Phast] Server-side performance metrics:\n";
        $output .= $log;
        $output .= ' -->';
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
