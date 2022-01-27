<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\Logger;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriter;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends PhastTestCase {
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var array
     */
    private $parsedElements;

    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter(URL::fromString(self::BASE_URL), true);
    }

    public function testShouldReturnApplied() {
        $this->shouldTransform();
        $buffer = "<html>\n<body></body>\n";
        $return = $this->filter->apply($buffer);
        $this->assertTrue(is_string($return));
        $this->assertNotEquals($buffer, $return);
    }

    public function testShouldApplyAllFilters() {
        $this->shouldTransform();
        $this->shouldTransform();
        $buffer = '<html><body></body></html>';
        $this->filter->apply($buffer);
    }

    public function testShouldOutputUTF8WithDeclaration() {
        $this->shouldTransform();
        $buffer = '<html><head><meta charset=utf8></head><body>ü</body></html>';
        $filtered = $this->filter->apply($buffer);
        $this->assertStringContainsString('ü', $filtered);
    }

    public function testShouldOutputUTF8WithoutDeclaration() {
        $this->shouldTransform();
        $buffer = '<html><body>ü</body></html>';
        $filtered = $this->filter->apply($buffer);
        $this->assertStringContainsString('ü', $filtered);
    }

    public function testHandleMixedUTF8AndWindows1252() {
        $this->shouldTransform();
        $buffer = "<html><body>ü\xfc</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertStringContainsString("ü\xfc", $filtered);
    }

    /**
     * @dataProvider shouldHandleTagCloseInScriptDataProvider
     */
    public function testShouldHandleTagCloseInScript($script) {
        $this->shouldTransform();
        $buffer = "<html><body>$script</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertStringStartsWith($buffer, $filtered);
        $this->assertNotEmpty($this->parsedElements);
    }

    public function shouldHandleTagCloseInScriptDataProvider() {
        return [
            ["<script>document.write('<div></div>');</script>"],
            ["<script type=\"text/javascript\">document.write('<div></div>');</script>"],
            ["<script>document.write('<script><\\/script>');</script>"],
            ["<script>document.write('<script><@/script>');</script>"],
        ];
    }

    public function testShouldAllowSelfClosingDiv() {
        $this->shouldTransform();
        $div = '<div /><span></span></div>';
        $buffer = "<html><body>$div</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertStringContainsString('<div /><span></span></div>', $filtered);
    }

    public function testShouldHandleExceptions() {
        $filter = $this->createMock(HTMLStreamFilter::class);
        $filter->expects($this->once())
            ->method('transformElements')
            ->willThrowException(new \Exception());
        $this->filter->addHTMLFilter($filter);
        $buffer = '<html><body></body></html>';

        $writer = $this->createMock(LogWriter::class);
        $msg = null;
        $writer->method('writeEntry')
            ->willReturnCallback(function (LogEntry $entry) use (&$msg) {
                if ($entry->getLevel() == LogLevel::CRITICAL) {
                    $msg = $entry->getMessage();
                }
            });
        Log::setLogger(new Logger($writer));
        $actual = $this->filter->apply($buffer);
        Log::initWithDummy();

        $this->assertStringStartsWith('Phast: CompositeHTMLFilter: ', $msg);
        $this->assertEquals($buffer, $actual);
    }

    public function testShouldAddStats() {
        $buffer = '<html><body></body></html>';
        $this->shouldTransform();
        $actual = $this->filter->apply($buffer);
        $this->assertStringContainsString('<!-- [Phast] Document optimized', $actual);
    }

    public function testShouldNotAddStats() {
        $this->filter = new Filter(URL::fromString(self::BASE_URL), false);
        $buffer = '<html><body></body></html>';
        $this->shouldTransform();
        $actual = $this->filter->apply($buffer);
        $this->assertStringNotContainsString('<!-- [Phast] Document optimized', $actual);
    }

    private function shouldTransform() {
        return $this->setExpectation($this->once());
    }

    private function shouldNotTransform() {
        return $this->setExpectation($this->never());
    }

    private function setExpectation($expectation) {
        $filterMock = $this->createMock(HTMLStreamFilter::class);
        $filterMock
            ->expects($expectation)
            ->method('transformElements')
            ->willReturnCallback(function (\Traversable $elements) {
                $this->parsedElements = $elements;
                return $elements;
            });
        $this->filter->addHTMLFilter($filterMock);
        return $filterMock;
    }
}
