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

    /**
     * CompositeHTMLFilter constructor.
     *
     * @param int $maxBufferSizeToApply
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
        $fixedBuffer = $this->cleanUTF8($buffer);
        $fixedBuffer = $this->escapeCloseTagInScript($fixedBuffer);
        $fixedBuffer = $this->fixIllegalSelfClosingTags($fixedBuffer);

        $xmlErrors = libxml_use_internal_errors(true);
        $doc = $this->dom;
        $doc->loadHTML('<?xml encoding="utf-8"?' . '>' . $fixedBuffer);

        $timings = [];

        foreach ($this->filters as $filter) {
            $this->logger()->info('Starting {filter}', ['filter' => get_class($filter)]);
            $time_filter_start = microtime(true);
            $filter->transformHTMLDOM($doc);
            $time_filter_delta = microtime(true) - $time_filter_start;
            $this->logger()->info(
                'Finished {filter} in {seconds}',
                ['filter' => get_class($filter), 'seconds' => $time_filter_delta]
            );
            $timings[get_class($filter)] = $time_filter_delta;
        }

        libxml_clear_errors();
        libxml_use_internal_errors($xmlErrors);

        // This gets us UTF-8 instead of entities
        $output = '<!doctype html>';
        foreach ($doc->childNodes as $node) {
            if (!$node instanceof \DOMDocumentType
                && !$node instanceof \DOMProcessingInstruction
            ) {
                $output .= $doc->saveHTML($node);
            }
        }

        $time_delta = microtime(true) - $time_start;

        $time_accounted = 0.;
        $log = "Page automatically optimized by Phast\n\n";
        arsort($timings);
        foreach ($timings as $cls => $time) {
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

    private function cleanUTF8($buffer) {
        // Treat every byte that is not valid UTF-8 as Windows-1252
        // https://www.w3.org/International/questions/qa-forms-utf-8
        return preg_replace_callback(
            '~
                [\x09\x0A\x0D\x20-\x7E]++          # ASCII
              | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
              |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
              | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
              |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
              |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
              | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
              |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
              | (.)
            ~xs',
            function($match) {
                if (isset($match[1]) && strlen($match[1])) {
                    return mb_convert_encoding($match[1], 'UTF-8', 'Windows-1252');
                } else {
                    return $match[0];
                }
            },
            $buffer
        );
    }

    private function escapeCloseTagInScript($buffer) {
        return preg_replace_callback(
            '~
                (<script(?:\s[^>]*)?>)
                (.*?)
                (</script)
            ~xsi',
            function ($match) {
                return $match[1] . str_replace('</', '<\\/', $match[2]) . $match[3];
            },
            $buffer
        );
    }

    private function fixIllegalSelfClosingTags($buffer) {
        // The tags are those HTML elements[1] that are not void[2].
        // 1: https://w3c.github.io/html-reference/elements.html
        // 2: https://w3c.github.io/html-reference/syntax.html#syntax-elements
        return preg_replace(
            '~
                (
                    <
                    (?>a|abbr|address|article|aside|audio|b|bdi|bdo|blockquote|body|button|canvas|caption|cite|code|colgroup|datalist|dd|del|details|dfn|div|dl|dt|em|fieldset|figcaption|figure|footer|form|h1|h2|h3|h4|h5|h6|head|header|hgroup|html|i|iframe|ins|kbd|label|legend|li|map|mark|menu|meter|nav|noscript|object|ol|optgroup|option|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|u|ul|var|video)
                    \b
                    (?:
                          [^"\'`/>]*+
                        | "[^"]*+"
                        | \'[^\']*+\'
                        | `[^`]*+`
                    )*
                )
                />
            ~xi',
            '\1>',
            $buffer
        );
    }

}
