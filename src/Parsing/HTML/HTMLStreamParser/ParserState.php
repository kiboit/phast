<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;

abstract class ParserState {

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * ParserState constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser) {
        $this->parser = $parser;
    }

    public function doctype($name, $idType = 0, $id = null, $quirks = false) {
        $this->parser->reset();
    }

    public function startTag($name, $attributes, $originalString, $selfClosing = false) {
        $this->parser->reset();
        // TODO: Return default text mode
        return 0;
    }

    public function endTag($name, $originalString) {
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
    }

    public function parseError($msg, $line, $col) {
        $this->parser->reset();
    }

    public function cdata($data) {
        $this->parser->reset();
    }

    public function processingInstruction($name, $data = null) {
        $this->parser->reset();
    }

}
