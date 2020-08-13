<?php

namespace Kibo\Phast\Filters\HTML\Composite;

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

    private $outputStats;

    /**
     * @var HTMLStreamFilter[]
     */
    private $filters = [];

    private $timings = [];

    /**
     * Filter constructor.
     * @param URL $baseUrl
     * @param $outputStats
     */
    public function __construct(URL $baseUrl, $outputStats) {
        $this->baseUrl = $baseUrl;
        $this->outputStats = $outputStats;
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
                    'line' => $e->getLine(),
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
            $elements = $filter->transformElements($elements, $context);
        }

        $output = '';

        foreach ($elements as $element) {
            $output .= $element;
        }

        $timeDelta = microtime(true) - $timeStart;

        if ($this->outputStats) {
            $output .= sprintf("\n<!-- [Phast] Document optimized in %dms -->\n", $timeDelta * 1000);
        }

        return $output;
    }

    public function selectFilters($callback) {
        $this->filters = array_filter($this->filters, $callback);
    }
}
