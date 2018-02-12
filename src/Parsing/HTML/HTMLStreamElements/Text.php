<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class Text extends Element {

    /**
     * @var string
     */
    private $text;

    /**
     * Text constructor.
     * @param string $text
     */
    public function __construct($text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }
}
