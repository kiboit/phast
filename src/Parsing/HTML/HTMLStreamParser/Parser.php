<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;


use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates\AwaitingTag;
use Kibo\Phast\Parsing\HTML\StringInputStream;

class Parser {

    /**
     * @var ParserState
     */
    private $state;

    /**
     * @var HTMLStream
     */
    private $htmlStream;

    /**
     * @var StringInputStream
     */
    private $inputStream;

    /**
     * @var int
     */
    private $caretPosition = 0;

    /**
     * Parser constructor.
     * @param HTMLStream $stream
     * @param StringInputStream $inputStream
     */
    public function __construct(HTMLStream $stream, StringInputStream $inputStream) {
        $this->htmlStream = $stream;
        $this->inputStream = $inputStream;
        $this->reset();
    }


    /**
     * @return HTMLStream
     */
    public function getHtmlStream() {
        return $this->htmlStream;
    }

    /**
     * @return StringInputStream
     */
    public function getInputStream() {
        return $this->inputStream;
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

    /**
     * @return int
     */
    public function getCaretPosition() {
        return $this->caretPosition;
    }

    /**
     * @param int $caretPosition
     */
    public function setCaretPosition($caretPosition) {
        $this->caretPosition = $caretPosition;
    }

    public function reset() {
        $this->state = new AwaitingTag($this);
    }

    public function doctype($name, $idType = 0, $id = null, $quirks = false) {
        $this->state->doctype($name, $idType, $id, $quirks);
    }

    public function startTag($name, $attributes, $startOffset, $endOffset, $selfClosing = false) {
        return $this->state->startTag($name, $attributes, $startOffset, $endOffset, $selfClosing);
    }

    public function endTag($name, $startOffset, $endOffset) {
        $this->state->endTag($name, $startOffset, $endOffset);
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
