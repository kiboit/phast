<?php

namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\PhastTestCase;

class URLTest extends PhastTestCase {
    public function testParsing() {
        $string = 'http://user:pass@test.com:8080/path/file.php?query#hash';
        $parsed = parse_url($string);
        $url = URL::fromString($string);

        foreach ($parsed as $key => $value) {
            $method = 'get' . ucfirst($key);
            $actual = $url->$method();
            $this->assertEquals($value, $actual, "Error for $key: expected $value, actual $actual");
        }
    }

    public function testCompiling() {
        $string = 'http://user:pass@test.com:8080/path/file.php?query#hash';
        $url = URL::fromString($string);
        $this->assertEquals($string, (string) $url);
    }

    /**
     * @dataProvider withBaseData
     */
    public function testWithBase($relative, $expected, $base = 'http://test.com/path/index.php?query#hash') {
        $base = URL::fromString($base);
        $relative = URL::fromString($relative);
        $result = $relative->withBase($base);
        $this->assertEquals($expected, (string) $result);
    }

    public function withBaseData() {
        return [
            ['relative/path', 'http://test.com/path/relative/path'],
            ['/absolute/path', 'http://test.com/absolute/path'],
            ['https://example.com', 'https://example.com'],
            ['?q#f', 'http://test.com/path/index.php?q#f'],
            ['//example.com', 'http://example.com'],
            ['relative/path', 'http://test.com/path/relative/path', 'http://test.com/path/?query#hash'],
            ['relative/path', 'http://test.com/path/relative/path', 'http://test.com/path/sub-path'],
        ];
    }

    /**
     * @dataProvider isLocalToData
     */
    public function testIsLocalTo($tested, $expected, $referred = 'http://test.com/path1') {
        $referred = URL::fromString($referred);
        $tested = URL::fromString($tested);
        $this->assertEquals($expected, $tested->isLocalTo($referred));
    }

    public function isLocalToData() {
        return [
            ['http://test.com/path2', true],
            ['https://test.com/path3', true],
            ['//test.com/path4', true],
            ['/path5', true],
            ['http://test.com/path1', false, '/path5'],
            ['http://example.com/path1', false],
        ];
    }

    /**
     * @dataProvider pathNormalisationData
     */
    public function testPathNormalisation($path, $expected) {
        $url = URL::fromString($path);
        $this->assertEquals($expected, (string) $url);
        $this->assertEquals($expected, $url->getPath());
    }

    public function pathNormalisationData() {
        return [
            ['../some-path', '../some-path'],
            ['some-path/../other-path', 'other-path'],
            ['some-path/../../other-path', '../other-path'],
            ['some-path/./sub-path', 'some-path/sub-path'],
            ['some-path////go-home', 'some-path/go-home'],
            ['some-path/0/go-home', 'some-path/0/go-home'],
            ['/../some-path/', '/../some-path/'],
            ['/some-path/../other/', '/other/'],
            ['/some-path/../', '/'],
            ['', ''],
        ];
    }

    /**
     * @dataProvider getExtensionData
     */
    public function testGetExtension($url, $expectedExtension = 'css') {
        $this->assertEquals($expectedExtension, URL::fromString($url)->getExtension());
    }

    public function getExtensionData() {
        return [
            ['http://example.com/the-style.css'],
            ['http://example.com/the-style.css?query'],
            ['http://example.com/the-style.css#hash'],
            ['http://example.com/the-style.pref.css'],
            ['http://example.com/the-style.pref.css?query'],
            ['http://example.com/the-style.pref.css#hash'],
            ['//example.com/the-style.css'],
            ['/path/the-style.css'],
            ['http://example.com/the-style.CSS', 'CSS'],
            ['http://example.com/the-image.png', 'png'],
            ['http://example.com/the-style.css?query'],
            ['http://example.com/the-style', ''],
        ];
    }

    /**
     * @dataProvider queryData
     */
    public function testWithQuery($input, $query, $output) {
        $url = URL::fromString($input)->withQuery($query);
        $this->assertEquals($output, (string) $url);
    }

    public function queryData() {
        return [
            ['http://x/', 'a', 'http://x/?a'],
            ['http://x/?a', 'b', 'http://x/?b'],
            ['http://x/?a', '', 'http://x/?'],
            ['http://x/?a', null, 'http://x/'],
        ];
    }

    public function testWithoutQuery() {
        $this->assertEquals('http://x/', URL::fromString('http://x/')->withoutQuery());
    }

    public function testRewrite() {
        $this->assertEquals(
            'http://google.com',
            URL::fromString('http://yahoo.com')
            ->rewrite(URL::fromString('http://yahoo.com/'), URL::fromString('http://google.com'))
        );

        $this->assertEquals(
            'http://google.com/test/abc',
            URL::fromString('http://yahoo.com/test/abc')
            ->rewrite(URL::fromString('http://yahoo.com/'), URL::fromString('http://google.com'))
        );

        $this->assertEquals(
            'http://google.com/new/abc',
            URL::fromString('http://yahoo.com/old/abc')
            ->rewrite(URL::fromString('http://yahoo.com/old'), URL::fromString('http://google.com/new'))
        );
    }

    public function testEncoding() {
        $this->assertEquals(
            'http://google.com/%D0%9C%D0%BD%D0%BE%D0%B3%D0%BE%20%D0%94%D0%BE%D0%B1%D1%80%D0%B5!.html',
            (string) URL::fromString('http://google.com/Много Добре!.html')
        );
    }

    public function testUserPass() {
        $this->assertEquals(
            'http://:0@x.com/',
            (string) URL::fromString('http://:0@x.com/')
        );
        $this->assertEquals(
            'http://0@x.com/',
            (string) URL::fromString('http://0@x.com/')
        );
    }
}
