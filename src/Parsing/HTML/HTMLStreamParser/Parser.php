<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;


use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates\AwaitingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates\AwaitingTagStart;

class Parser {

    /**
     * @var ParserState
     */
    private $state;

    /**
     * @var HTMLStream
     */
    private $stream;

    /**
     * HTMLStreamParser constructor.
     * @param HTMLStream $stream
     */
    public function __construct(HTMLStream $stream) {
        $this->stream = $stream;
        $this->reset();
    }

    /**
     * @return ParserState
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @param ParserState $state
     */
    public function setState(ParserState $state) {
        $this->state = $state;
    }

    public function reset() {
        $this->state = new AwaitingTag($this);
    }

    /**
     * @return HTMLStream
     */
    public function getStream() {
        return $this->stream;
    }

    public function doctype($name, $idType = 0, $id = null, $quirks = false) {
        $this->state->doctype($name, $idType, $id, $quirks);
    }

    public function startTag($name, $attributes, $originalString, $selfClosing = false) {
        return $this->state->startTag($name, $attributes, $originalString, $selfClosing);
    }

    public function endTag($name, $originalString) {
        $this->state->endTag($name, $originalString);
    }

    public function comment($cdata) {
        $this->state->comment($cdata);
    }

    public function text($cdata) {
        $this->state->text($cdata);
    }

    public function eof() {
        $this->state->eof();
    }

    public function parseError($msg, $line, $col) {
        $this->state->parseError($msg, $line, $col);
    }

    public function cdata($data) {
        $this->state->cdata($data);
    }

    public function processingInstruction($name, $data = null) {
        $this->state->processingInstruction($name, $data);
    }

}
