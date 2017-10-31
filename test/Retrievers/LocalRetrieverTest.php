<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\FileSystem\FileSystemAccessor;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class LocalRetrieverTest extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $accessor;

    /**
     * @var LocalRetriever
     */
    private $retriever;

    private $retrievedFile;

    public function setUp() {
        parent::setUp();
        $this->retrievedFile = null;
        $this->accessor = $this->createMock(FileSystemAccessor::class);
        $this->retriever = new LocalRetriever(['kibo.test' => 'local-test'], $this->accessor);
    }

    public function testMapping() {
        $this->accessor->method('realpath')
                 ->willReturnCallback(function ($path) {
                     return trim($path, '/');
                 });
        $this->accessor
            ->expects($this->once())
            ->method('file_get_contents')
            ->willReturnCallback(function ($file) {
                $this->retrievedFile = $file;
                return 'returned';
            });
        $this->assertEquals('returned', $this->retriever->retrieve(URL::fromString('http://kibo.test/local.file')));
        $this->assertEquals('local-test/local.file', $this->retrievedFile);

        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://test.com/make.me')));
    }

    public function testForbiddenPaths() {
        $this->accessor->expects($this->never())
            ->method('file_get_contents');
        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://kibo.test/../forbidden')));
    }

    public function testGetLastModificationTime() {
        $url1 = URL::fromString('http://kibo.test/../forbiden');
        $url2 = URL::fromString('http://kibo.test/path2');
        $this->accessor->expects($this->once())
            ->method('filemtime')
            ->with('local-test/path2')
            ->willReturn(123);
        $this->assertFalse($this->retriever->getLastModificationTime($url1));
        $this->assertEquals(123, $this->retriever->getLastModificationTime($url2));
    }


}
