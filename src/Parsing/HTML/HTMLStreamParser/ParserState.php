<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;

use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\StringInputStream;

abstract class ParserState {

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var StringInputStream
     */
    protected $inputStream;

    /**
     * @var HTMLStream
     */
    protected $htmlStream;

    /**
     * ParserState constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser) {
        $this->parser = $parser;
        $this->inputStream = $parser->getInputStream();
        $this->htmlStream = $parser->getHtmlStream();
    }

    public function doctype($name, $idType = 0, $id = null, $quirks = false) {
        $this->parser->reset();
    }

    public function startTag($name, $attributes, $startOffset, $endOffset, $selfClosing = false) {
        $this->parser->reset();
        return 0;
    }

    public function endTag($name, $startOffset, $endOffset) {
        $this->parser->reset();
    }

    public function comment($cdata) {
        $this->parser->reset();
    }

    public function text($cdata) {
        $this->parser->reset();
    }

    public function eof() {
        $this->parser->reset();
        // TODO: Handle infinite loop
        $this->parser->eof();
    }

    public function parseError($msg, $line, $col) {}

    public function cdata($data) {
        $this->parser->reset();
    }

    public function processingInstruction($name, $data = null) {
        $this->parser->reset();
    }

}
