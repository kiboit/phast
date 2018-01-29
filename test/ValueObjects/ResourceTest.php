<?php

namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Retrievers\Retriever;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase {

    /**
     * @var URL
     */
    private $url;

    private $mimeType = 'text/css';

    private $content = 'the-content';

    public function setUp() {
        parent::setUp();
        $this->url = URL::fromString('http://phast.test');
    }

    public function testMakeWithContent() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $this->checkResource($resource);
    }

    public function testMakeWithRetriever() {
        $retriever = $this->makeContentRetriever();
        $resource = Resource::makeWithRetriever($this->url, $retriever, $this->mimeType);
        $this->checkResource($resource);
    }

    public function testRetrievingOnlyOnce() {
        $retriever = $this->makeContentRetriever();
        $resource = Resource::makeWithRetriever($this->url, $retriever, $this->mimeType);
        $resource->getContent();
        $resource->getContent();
    }

    public function testGetLastModificationTimeWithRetriever() {
        $retriever = $this->createMock(Retriever::class);
        $retriever->expects($this->once())
            ->method('getLastModificationTime')
            ->with($this->url)
            ->willReturn(123);
        $resource = Resource::makeWithRetriever($this->url, $retriever, $this->mimeType);

        $this->assertEquals(123, $resource->getLastModificationTime());
    }

    public function testGetLastModificationTimeWithoutRetriever() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $this->assertSame(0, $resource->getLastModificationTime());
    }

    public function testContentModification() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $newResource = $resource->withContent('new-content');
        $this->assertNotSame($newResource, $resource);
        $this->assertSame('new-content', $newResource->getContent());
        $this->assertSame($this->mimeType, $newResource->getMimeType());
    }

    public function testContentAndMimeTypeModification() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $newResource = $resource->withContent('new-content', 'new-mime-type');
        $this->assertSame('new-content', $newResource->getContent());
        $this->assertSame('new-mime-type', $newResource->getMimeType());
    }

    private function makeContentRetriever() {
        $retriever = $this->createMock(Retriever::class);
        $retriever->expects($this->once())
            ->method('retrieve')
            ->with($this->url)
            ->willReturn($this->content);
        return $retriever;
    }

    private function checkResource(Resource $resource) {
        $this->assertSame($this->url, $resource->getUrl());
        $this->assertSame($this->mimeType, $resource->getMimeType());
        $this->assertSame($this->content, $resource->getContent());
    }

}
