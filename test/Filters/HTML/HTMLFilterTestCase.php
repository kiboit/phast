<?php

namespace Kibo\Phast\Filters\HTML;

use PHPUnit\Framework\TestCase;

class HTMLFilterTestCase extends TestCase {

    const BASE_URL = 'kibo-test.org';

    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var \DOMElement
     */
    protected $html;

    /**
     * @var \DOMElement
     */
    protected $head;

    /**
     * @var \DOMElement
     */
    protected $body;

    public function setUp() {
        parent::setUp();

        $this->dom = new \DOMDocument();
        $this->html = $this->dom->createElement('html');
        $this->dom->appendChild($this->html);
        $this->head = $this->dom->createElement('head');
        $this->html->appendChild($this->head);
        $this->body = $this->dom->createElement('body');
        $this->html->appendChild($this->body);
    }

}
