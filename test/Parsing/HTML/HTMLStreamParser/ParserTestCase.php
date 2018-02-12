<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;


use Kibo\Phast\Parsing\HTML\HTMLStream;
use Masterminds\HTML5\Parser\InputStream;
use PHPUnit\Framework\TestCase;

abstract class ParserTestCase extends TestCase {

    /**
     * @var InputStream
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
        $this->inputStream = $this->createMock(InputStream::class);
        $this->htmlStream = new HTMLStream($this->inputStream);
        $this->parser = new Parser($this->htmlStream);
    }

}
