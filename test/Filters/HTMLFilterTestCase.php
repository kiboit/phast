<?php

namespace Kibo\Phast\Filters;

use PHPUnit\Framework\TestCase;

class HTMLFilterTestCase extends TestCase {

    /**
     * @var HTMLFilter
     */
    private $filter;

    public function setUp() {
        parent::setUp();
        $this->filter = $this->getMockForAbstractClass(HTMLFilter::class);
    }

    public function testShouldApplyOnHTML() {
        $this->shouldTransform();
        $buffer = "<!DOCTYPE html>\n<html>\n<body></body>\n</html>";
        $this->filter->apply($buffer);
    }

    public function testShouldApplyOnXHTML() {
        $this->shouldTransform();
        $buffer = "<?xml version=\"1.0\"?><!DOCTYPE html>\n<html>\n<body></body>\n</html>";
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

    public function testShouldNotApplyWithNoBodyEndTag() {
        $this->shouldNotTransform();
        $buffer = "<html>\n<body>";
        $this->filter->apply($buffer);
    }

    public function testShouldNotApplyIfNotHTML() {
        $this->shouldNotTransform();
        $buffer = '<?xml version="1.0"?><tag>asd</tag>';
        $this->filter->apply($buffer);
    }

    public function testNotLoadingBadHTML() {
        $this->shouldNotTransform();
        $buffer = "\0<html><body></body></html>";
        $doc = new \DOMDocument();
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

    private function setExpectation($expectation) {
        return $this->filter->expects($expectation)->method('transformHTML');
    }

    private function shouldTransform() {
        return $this->setExpectation($this->once());
    }

    private function shouldNotTransform() {
        return $this->setExpectation($this->never());
    }

}
