<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;


use Kibo\Phast\Parsing\HTML\HTMLStream;
use Kibo\Phast\Parsing\HTML\StringInputStream;
use PHPUnit\Framework\TestCase;

abstract class ParserTestCase extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $inputStream;

    /**
     * @var HTMLStream
     */
    protected $htmlStream;

    /**
     * @var Parser
     */
    protected $parser;

    public function setUp() {
        parent::setUp();
        $this->inputStream = $this->createMock(StringInputStream::class);
        $this->htmlStream = new HTMLStream();
        $this->parser = new Parser($this->htmlStream, $this->inputStream);
    }

}
