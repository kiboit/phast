<?php

namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Exceptions\ItemNotFoundException;
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

    public function testgetCacheSaltWithRetriever() {
        $retriever = $this->createMock(Retriever::class);
        $retriever->expects($this->once())
            ->method('getCacheSalt')
            ->with($this->url)
            ->willReturn(123);
        $resource = Resource::makeWithRetriever($this->url, $retriever, $this->mimeType);

        $this->assertEquals(123, $resource->getCacheSalt());
    }

    public function testgetCacheSaltWithoutRetriever() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $this->assertSame(0, $resource->getCacheSalt());
    }

    public function testContentModification() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $newResource = $resource->withContent('new-content');
        $this->assertNotSame($newResource, $resource);
        $this->assertSame('new-content', $newResource->getContent());
        $this->assertSame($this->mimeType, $newResource->getMimeType());
    }

    public function testDependenciesAdding() {
        $resource = Resource::makeWithContent($this->url, $this->content);
        $this->assertEmpty($resource->getDependencies());

        $deps = [
            Resource::makeWithContent($this->url, $this->content),
            Resource::makeWithContent($this->url, $this->content)
        ];
        $new = $resource->withDependencies($deps);
        $this->assertNotSame($resource, $new);
        $actualDeps = $new->getDependencies();
        $this->assertCount(2, $actualDeps);
        $this->assertSame($deps[0], $actualDeps[0]);
        $this->assertSame($deps[1], $actualDeps[1]);
    }

    public function testContentAndMimeTypeModification() {
        $resource = Resource::makeWithContent($this->url, $this->content, $this->mimeType);
        $newResource = $resource->withContent('new-content', 'new-mime-type');
        $this->assertSame('new-content', $newResource->getContent());
        $this->assertSame('new-mime-type', $newResource->getMimeType());
    }

    public function testExceptionOnNotFoundResource() {
        $retriever = $this->makeContentRetriever(false);
        $resource = Resource::makeWithRetriever($this->url, $retriever);
        $this->expectException(ItemNotFoundException::class);
        $resource->getContent();
    }

    public function testNoExceptionOnEmptyContent() {
        $retriever = $this->makeContentRetriever('');
        $resource = Resource::makeWithRetriever($this->url, $retriever);
        $this->assertEmpty($resource->getContent());

    }

    private function makeContentRetriever($content = null) {
        $content = is_null($content) ? $this->content : $content;
        $retriever = $this->createMock(Retriever::class);
        $retriever->expects($this->once())
            ->method('retrieve')
            ->with($this->url)
            ->willReturn($content);
        return $retriever;
    }

    private function checkResource(Resource $resource) {
        $this->assertSame($this->url, $resource->getUrl());
        $this->assertSame($this->mimeType, $resource->getMimeType());
        $this->assertSame($this->content, $resource->getContent());
    }

}
