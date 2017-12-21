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

    private $retrievedFile;

    public function setUp() {
        parent::setUp();
        $this->retrievedFile = null;
        $this->funcs = new ObjectifiedFunctions();
        $this->retriever = new LocalRetriever(['kibo.test' => 'local-test'], $this->funcs);
        $this->funcs->file_get_contents = function ($file) {
            $this->retrievedFile = $file;
            return 'returned';
        };
        $this->funcs->realpath = function ($path) {
            return $path;
        };
    }

    public function testMapping() {
        $this->assertEquals('returned', $this->retriever->retrieve(URL::fromString('http://kibo.test/local.css')));
        $this->assertEquals('local-test/local.css', $this->retrievedFile);
        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://test.com/make.css')));
    }

    public function testDirectoryPrefixing() {
        $map = [
            'kibo.test' => [
                '/dir1' => '/the-dir-1',
                '/dir2' => '/the-dir-2',
                '/dir1/subdir' => '/the-sub-dir'
            ]
        ];
        $retriever = new LocalRetriever($map, $this->funcs);

        $this->retrievedFile = null;
        $retriever->retrieve(URL::fromString('http://kibo.test/dir1/dir1-file.css'));
        $this->assertEquals('/the-dir-1/dir1-file.css', $this->retrievedFile);

        $this->retrievedFile = null;
        $retriever->retrieve(URL::fromString('http://kibo.test/dir2/dir2-file.css'));
        $this->assertEquals('/the-dir-2/dir2-file.css', $this->retrievedFile);

        $this->retrievedFile = null;
        $retriever->retrieve(URL::fromString('http://kibo.test/dir1/subdir/subdir-file.css'));
        $this->assertEquals('/the-sub-dir/subdir-file.css', $this->retrievedFile);
    }

    public function testNotGettingForbiddenPaths() {
        $this->funcs->file_get_contents = function ($file) {
            $this->fail("file_get_contents() must not be called! File: $file");
        };
        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://kibo.test/../forbidden')));
    }


    public function testNotGettingNotAllowed() {
        $this->funcs->file_get_contents = function () {
            $this->fail('file_get_content() must not be called');
        };
        $this->retriever->retrieve(URL::fromString('http://kibo.test/local.php'));
    }

    /**
     * @dataProvider gettingAllowedExtensionsData
     */
    public function testGettingOnlyAllowedExtensions($extension) {
        $this->funcs->file_get_contents = function ($file) {
            return $file;
        };
        $file = 'file.' . $extension;
        $actual = $this->retriever->retrieve(URL::fromString("http://kibo.test/$file"));
        $this->assertEquals("local-test/$file", $actual);
    }

    public function gettingAllowedExtensionsData() {
        $extensions = LocalRetriever::getAllowedExtensions();
        $upper = array_map('strtoupper', $extensions);
        $dataSet = [];
        foreach (array_merge($extensions, $upper) as $ext) {
            $dataSet[] = [$ext];
        }
        return $dataSet;
    }

    public function testGetLastModificationTime() {
        $url1 = URL::fromString('http://kibo.test/../forbiden');
        $url2 = URL::fromString('http://kibo.test/path2');
        $this->funcs->filemtime = function ($path) {
            $this->assertEquals('local-test/path2', $path);
            return 123;
        };
        $this->assertFalse($this->retriever->getLastModificationTime($url1));
        $this->assertEquals(123, $this->retriever->getLastModificationTime($url2));
    }


}
