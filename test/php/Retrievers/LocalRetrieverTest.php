<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class LocalRetrieverTest extends TestCase {
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $funcs;

    /**
     * @var LocalRetriever
     */
    private $retriever;

    private $calledForFile;

    public function setUp(): void {
        parent::setUp();
        $this->calledForFile = null;
        $this->funcs = new ObjectifiedFunctions();
        $this->retriever = new LocalRetriever(['kibo.test' => 'local-test'], $this->funcs);
        $this->funcs->realpath = function ($path) {
            return $path;
        };
    }

    /**
     * @dataProvider retrieverMethods
     */
    public function testMapping($method, $calledFunction) {
        $this->funcs->$calledFunction = function ($file) {
            $this->calledForFile = $file;
            return 'returned';
        };
        $this->assertStringStartsWith(
            'returned',
            $this->retriever->$method(URL::fromString('http://kibo.test/local.css'))
        );
        $this->assertEquals('local-test/local.css', $this->calledForFile);
    }

    /**
     * @dataProvider directoryPrefixingData
     */
    public function testDirectoryPrefixing($method, $calledFunction, $input, $expected) {
        $this->funcs->$calledFunction = function ($file) {
            $this->calledForFile = $file;
        };
        $map = [
            'kibo.test' => [
                '/dir1' => '/the-dir-1',
                '/dir2' => '/the-dir-2',
                '/pub/version\d+/' => '/var/www/pub',
                '/dir~' => '/dir-tilde',
                'dir3' => '/the-dir-3',
                'dir4/' => '/the-dir-4',
                '/dir1/subdir' => '/the-sub-dir',
            ],
        ];
        $retriever = new LocalRetriever($map, $this->funcs);
        $retriever->$method(URL::fromString($input));
        $this->assertEquals($expected, $this->calledForFile);
    }

    public function directoryPrefixingData() {
        return $this->allMethodsWithData([
            ['http://kibo.test/dir1/dir1-file.css', '/the-dir-1/dir1-file.css'],
            ['http://kibo.test/dir2/dir2-file.css', '/the-dir-2/dir2-file.css'],
            ['http://kibo.test/dir1/subdir/subdir-file.css', '/the-sub-dir/subdir-file.css'],
            ['http://kibo.test/pub/version1234/abcd.gif', '/var/www/pub/abcd.gif'],
            ['http://kibo.test/dir~/dir~-file.css', '/dir-tilde/dir~-file.css'],
            ['http://kibo.test/dir3/dir3-file.css', '/the-dir-3/dir3-file.css'],
            ['http://kibo.test/not/dir3/dir3-file.css', null],
            ['http://kibo.test/dir4/dir4-file.css', '/the-dir-4/dir4-file.css'],
            ['http://kibo.test/dir2abc/dir2-file.css', null],
            ['http://kibo.test/dir1/hello%20world.css', '/the-dir-1/hello world.css'],
        ]);
    }

    /**
     * @dataProvider notGettingForbiddenData
     */
    public function testNotGettingForbidden($method, $calledFunction, $urlString) {
        $this->funcs->$calledFunction = function ($file) {
            $this->fail("file_get_contents() must not be called! File: $file");
        };
        $url = URL::fromString($urlString);
        $this->assertFalse($this->retriever->$method($url));
    }

    public function notGettingForbiddenData() {
        return $this->allMethodsWithData([
            ['http://kibo.test/../forbidden-path'],
            ['http://kibo.test/forbidden-extension.php'],
            ['http://unmpapped-domain.test/make.css'],
        ]);
    }

    /**
     * @dataProvider gettingAllowedExtensionsData
     */
    public function testGettingAllowedExtensions($method, $calledFunction, $extension) {
        $this->funcs->$calledFunction = function ($file) {
            return $file;
        };
        $file = 'file.' . $extension;
        $url = URL::fromString("http://kibo.test/$file");
        $actual = $this->retriever->$method($url);
        $this->assertNotFalse($actual);
    }

    public function gettingAllowedExtensionsData() {
        $extensions = LocalRetriever::getAllowedExtensions();
        $upper = array_map('strtoupper', $extensions);
        $allExtensions = array_merge($extensions, $upper);
        return $this->allMethodsWithData($allExtensions);
    }

    public function retrieverMethods() {
        return [
            ['retrieve', 'file_get_contents'],
            ['getCacheSalt', 'filectime'],
            ['getSize', 'filesize'],
        ];
    }

    private function allMethodsWithData(array $data) {
        foreach ($this->retrieverMethods() as $callables) {
            foreach ($data as $params) {
                yield array_merge($callables, (array) $params);
            }
        }
    }

    public function testEmptyCacheSaltForNonExistentFile() {
        $fns = new ObjectifiedFunctions();
        $retriever = new LocalRetriever(['test.com' => '/test'], $fns);
        $url = URL::fromString('http://test.com/hello.gif');
        $this->assertSame('', $retriever->getCacheSalt($url));
    }
}
