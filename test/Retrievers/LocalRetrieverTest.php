<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\FileSystem\FileSystemAccessor;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class LocalRetrieverTest extends TestCase {

    /**
     * @var LocalRetriever
     */
    private $retriever;

    private $retrievedFile;

    public function setUp() {
        parent::setUp();
        $this->retrievedFile = null;
        $accessor = $this->createMock(FileSystemAccessor::class);
        $accessor->method('realpath')
            ->willReturnCallback(function ($path) {
                return trim($path, '/');
            });
        $accessor->method('file_get_contents')->willReturnCallback(function ($file) {
            $this->retrievedFile = $file;
            return 'returned';
        });
        $this->retriever = new LocalRetriever(['kibo.test' => 'local-test'], $accessor);
    }

    public function testMapping() {
        $this->assertEquals('returned', $this->retriever->retrieve(URL::fromString('http://kibo.test/local.file')));
        $this->assertEquals('local-test//local.file', $this->retrievedFile);

        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://test.com/make.me')));
    }



}
