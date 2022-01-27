<?php
namespace Kibo\Phast\Filters\HTML\MetaCharset;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {
    public function setUp(): void {
        parent::setUp();
        $this->filter = new Filter();
    }

    /** @dataProvider insertRemoveCharsetData */
    public function testInsertRemoveCharset($html, $expected) {
        $actual = $this->applyFilter($html, true);
        $this->assertStringStartsWith($expected, $actual);
    }

    public function insertRemoveCharsetData() {
        yield [
            '<html><head><style></style><meta charset="YOLO"></head><body></body></html>',
            '<html><head><meta charset="utf-8"><style></style></head><body></body></html>',
        ];

        yield [
            '<style></style><meta charset="YOLO"></head><body></body></html>',
            '<meta charset="utf-8"><style></style></head><body></body></html>',
        ];

        yield [
            '<!-- YOLO --><style></style><meta charset="YOLO"></head><body></body></html>',
            '<!-- YOLO --><meta charset="utf-8"><style></style></head><body></body></html>',
        ];

        yield [
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><body></body></html>',
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta charset="utf-8"><title></title></head><body></body></html>',
        ];
    }
}
