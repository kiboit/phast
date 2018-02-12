<?php


namespace Kibo\Phast\Parsing\HTML;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Masterminds\HTML5\Parser\InputStream;

class HTMLStream {

    /**
     * @var InputStream
     */
    private $dataStream;

    /**
     * @var Element[]
     */
    private $elements = [];

    /**
     * HTMLStream constructor.
     * @param InputStream $dataStream
     */
    public function __construct(InputStream $dataStream) {
        $this->dataStream = $dataStream;
    }

    /**
     * @param Element $element
     */
    public function addElement(Element $element) {
        $this->elements[] = $element;
    }


    /**
     * @return Element[]
     */
    public function getElements() {
        return $this->elements;
    }

}
