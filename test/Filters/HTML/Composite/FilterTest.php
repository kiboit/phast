<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\Logger;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {

    const MAX_BUFFER_SIZE_TO_APPLY = 1024;

    /**
     * @var Filter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = new Filter(self::MAX_BUFFER_SIZE_TO_APPLY, new DOMDocument());
    }

    public function testShouldApplyOnHTML() {
        $this->shouldTransform();
        $buffer = "<!DOCTYPE html>\n<html>\n<body></body>\n</html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertRegExp("~^<!doctype html><html>\s*<body></body>\s*</html>~", $filtered);
    }

    public function testShouldApplyOnXHTML() {
        $this->shouldTransform();
        $buffer = "<?xml version=\"1.0\"?\><!DOCTYPE html>\n<html>\n<body></body>\n</html>";
        $this->filter->apply($buffer);
    }

    public function testShouldApplyOnLowerDOCTYPE() {
        $this->shouldTransform();
        $buffer = "<!doctype html>\n<html>\n<body></body>\n</html>";
        $this->filter->apply($buffer);
    }

    public function testShouldApplyWithNoDOCTYPE() {
        $this->shouldTransform();
        $buffer = "<html>\n<body></body>\n</html>";
        $this->filter->apply($buffer);
    }

    public function testShouldApplyWithWhitespacesStart() {
        $this->shouldTransform();
        $buffer = "    \n<!doctype       html>\n<html>\n<body></body>\n</html>";
        $this->filter->apply($buffer);
    }

    public function testShouldApplyWithComments() {
        $this->shouldTransform();
        $buffer = "<!doctype html>\n<!-- hello -->\n<html>\n<body></body>\n</html>";
        $this->filter->apply($buffer);
    }

    public function testShouldNotApplyWithNoBodyEndTag() {
        $this->shouldNotTransform();
        $buffer = "<html>\n<body>";
        $this->filter->apply($buffer);
    }

    public function testShouldNotApplyIfNotHTML() {
        $this->shouldNotTransform();
        $buffer = '<?xml version="1.0"?\><tag>asd</tag>';
        $this->filter->apply($buffer);
    }

    public function testNotLoadingBadHTML() {
        $this->shouldNotTransform();
        $buffer = "\0<html><body></body></html>";
        $doc = new \Kibo\Phast\Common\DOMDocument();
        $loads = @$doc->loadHTML($buffer);
        $this->assertFalse($loads);
        $this->assertEquals($buffer, $this->filter->apply($buffer));
    }

    public function testShouldReturnApplied() {
        $this->shouldTransform();
        $buffer = "<html>\n<body></body>\n";
        $return = $this->filter->apply($buffer);
        $this->assertTrue(is_string($return));
        $this->assertNotEquals($buffer, $return);
    }

    public function testShouldReturnOriginal() {
        $this->shouldNotTransform();
        $buffer = 'yolo';
        $this->assertEquals($buffer, $this->filter->apply($buffer));
    }

    public function testShouldApplyAllFilters() {
        $this->shouldTransform();
        $this->shouldTransform();
        $buffer = '<html><body></body></html>';
        $this->filter->apply($buffer);
    }

    public function testShouldNotApplyIfBufferIsTooBig() {
        $this->shouldNotTransform();
        $buffer = sprintf('<html><body>%s</body></html>', str_pad('', self::MAX_BUFFER_SIZE_TO_APPLY, 's'));
        $filtered = $this->filter->apply($buffer);
        $this->assertEquals($buffer, $filtered);
    }

    public function testShouldNotApplyToAmpWithAsciiDeclaration() {
        $this->shouldNotTransform();
        $buffer = '<!doctype html><html amp><body></body></html>';
        $filtered = $this->filter->apply($buffer);
        $this->assertEquals($buffer, $filtered);
    }

    public function testShouldNotApplyToAmpWithUnicodeDeclaration() {
        $this->shouldNotTransform();
        $buffer = '<!doctype html><html ⚡><body></body></html>';
        $filtered = $this->filter->apply($buffer);
        $this->assertEquals($buffer, $filtered);
    }

    public function testShouldOutputUTF8WithDeclaration() {
        $this->shouldTransform();
        $buffer = '<html><head><meta charset=utf8></head><body>ü</body></html>';
        $filtered = $this->filter->apply($buffer);
        $this->assertContains('ü', $filtered);
    }

    public function testShouldOutputUTF8WithoutDeclaration() {
        $this->shouldTransform();
        $buffer = '<html><body>ü</body></html>';
        $filtered = $this->filter->apply($buffer);
        $this->assertContains('ü', $filtered);
    }

    public function testHandleMixedUTF8AndWindows1252() {
        $this->shouldTransform();
        $buffer = "<html><body>ü\xfc</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertContains('üü', $filtered);
    }

    public function testHandleMixedUTF8AndWindows1252WithEuroSign() {
        $this->shouldTransform();
        $buffer = "<html><body>ü\x80</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertContains('ü€', $filtered);
    }

    /**
     * @dataProvider shouldHandleTagCloseInScriptDataProvider
     */
    public function testShouldHandleTagCloseInScript($script) {
        $this->shouldTransform();
        $buffer = "<html><body>$script</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertContains($script, str_replace('\\', '', $filtered));
    }

    public function shouldHandleTagCloseInScriptDataProvider() {
        return [
            ["<script>document.write('<div></div>');</script>"],
            ["<script type=\"text/javascript\">document.write('<div></div>');</script>"]
        ];
    }

    public function testShouldAllowSelfClosingDiv() {
        $this->shouldTransform();
        $div = "<div /><span></span></div>";
        $buffer = "<html><body>$div</body></html>";
        $filtered = $this->filter->apply($buffer);
        $this->assertContains('<div><span></span></div>', $filtered);
    }

    public function testShouldHandleExceptions() {
        $filter = $this->createMock(HTMLFilter::class);
        $filter->expects($this->once())
            ->method('transformHTMLDOM')
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

    private function setExpectation($expectation) {
        $filterMock = $this->createMock(HTMLFilter::class);
        $filterMock->expects($expectation)->method('transformHTMLDOM');
        $this->filter->addHTMLFilter($filterMock);
        return $filterMock;
    }

    private function shouldTransform() {
        return $this->setExpectation($this->once());
    }

    private function shouldNotTransform() {
        return $this->setExpectation($this->never());
    }

}
