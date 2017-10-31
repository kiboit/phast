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
    }

    public function testMapping() {
        $this->funcs->file_get_contents = function ($file) {
            $this->retrievedFile = $file;
            return 'returned';
        };
        $this->assertEquals('returned', $this->retriever->retrieve(URL::fromString('http://kibo.test/local.file')));
        $this->assertEquals('local-test/local.file', $this->retrievedFile);

        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://test.com/make.me')));
    }

    public function testForbiddenPaths() {
        $this->funcs->file_get_contents = function () {
            $this->fail('file_get_contents() must not be called');
        };
        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://kibo.test/../forbidden')));
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
