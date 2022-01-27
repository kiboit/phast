<?php

namespace Kibo\Phast;

/**
 * @runTestsInSeparateProcesses
 */
class PhastDocumentFiltersTest extends \PHPUnit\Framework\TestCase {
    public function setUp(): void {
        $_SERVER += [
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/',
        ];
    }

    public function testDeploy() {
        $handlersBefore = ob_list_handlers();

        PhastDocumentFilters::deploy([]);

        $handlersAfter = ob_list_handlers();

        $this->assertNotEquals($handlersBefore, $handlersAfter);
    }

    /** @dataProvider shouldApplyData */
    public function testShouldApply($buffer, $documentsOnly = true) {
        $config = ['optimizeHTMLDocumentsOnly' => $documentsOnly];
        $out = PhastDocumentFilters::apply($buffer, $config);
        $this->assertFiltersApplied($out);
    }

    public function shouldApplyData() {
        yield ['<!doctype html><html><head><title>Hello, World!</title></head><body></body></html>'];
        yield ["<!DOCTYPE html>\n<html>\n<body></body>\n</html>"];
        yield ["<?xml version=\"1.0\"?\><!DOCTYPE html>\n<html>\n<body></body>\n</html>"];
        yield ["<!doctype html>\n<html>\n<body></body>\n</html>"];
        yield ["<html>\n<body></body>\n</html>"];
        yield ["    \n<!doctype       html>\n<html>\n<body></body>\n</html>"];
        yield ["<!doctype html>\n<!-- hello -->\n<html>\n<body></body>\n</html>"];
        yield ["<!-- hello -->\n<!doctype html>\n<html>\n<body></body>\n</html>"];
        yield ['<b>Yup</b>', false];
    }

    /**
     * @dataProvider shouldNotApplyData
     */
    public function testShouldNotApply($buffer, $documentsOnly = true) {
        $config = ['optimizeHTMLDocumentsOnly' => $documentsOnly];
        $out = PhastDocumentFilters::apply($buffer, $config);
        $this->assertFiltersNotApplied($out);
    }

    public function shouldNotApplyData() {
        yield ["<html>\n<body>"];
        yield ['<?xml version="1.0"?\><tag>asd</tag>'];
        yield ["\0<html><body></body></html>"];
        yield ['Not html', false];
        yield ['{"html":"<json/>"}', false];
    }

    /**
     * @dataProvider jsonData
     */
    public function testJson($shouldApply, $buffer, $documentsOnly) {
        $config = [
            'optimizeHTMLDocumentsOnly' => $documentsOnly,
            'optimizeJSONResponses' => true,
        ];
        $out = PhastDocumentFilters::apply($buffer, $config);
        if ($shouldApply) {
            $this->assertFiltersApplied($out);
        } else {
            $this->assertFiltersNotApplied($out);
        }
    }

    public function jsonData() {
        yield [true, '{"html":"<json/>"}', false];
        yield [false, '{"something":"<json/>"}', false];
    }

    /**
     * @dataProvider shouldNotInjectScriptsData
     */
    public function testShouldNotInjectScripts($buffer) {
        $out = PhastDocumentFilters::apply($buffer, []);
        $this->assertFiltersApplied($out);
        $this->assertStringNotContainsString('<script', $out);
    }

    public function shouldNotInjectScriptsData() {
        yield ['<!doctype html><html amp><body></body></html>'];
        yield ['<!doctype html><html ⚡><body></body></html>'];
    }

    public function testOptimizeImagesInAmpDocument() {
        $out = PhastDocumentFilters::apply(
            '<!doctype html><html amp><body><img src=batman.jpg></body></html>',
            [
                'retrieverMap' => [
                    'example.com' => __DIR__ . '/../test-app/images',
                ],
            ]
        );
        $this->assertFiltersApplied($out);
        $this->assertStringNotContainsString('<script', $out);
        $this->assertStringContainsString('phast.php', $out);
    }

    private function assertFiltersApplied($out) {
        $this->assertStringContainsString('[Phast]', $out);
    }

    private function assertFiltersNotApplied($out) {
        $this->assertStringNotContainsString('[Phast]', $out);
    }
}
