<?php
namespace Kibo\Phast\Filters\HTML\MetaCharset;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;

class FilterTest extends HTMLFilterTestCase {
    public function setUp() {
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
    }
}
