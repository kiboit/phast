<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class Text {

    /**
     * @var
     */
    private $text;

    /**
     * Text constructor.
     * @param $text
     */
    public function __construct($text) {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->text;
    }
}
