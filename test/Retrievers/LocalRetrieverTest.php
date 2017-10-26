<?php

namespace Kibo\Phast\Retrievers;

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
        $this->retriever = new LocalRetriever(['kibo.test' => 'local-test'], function ($file, $base) {
            $this->retrievedFile = $base . $file;
            return 'returned';
        });
    }

    public function testMapping() {
        $this->assertEquals('returned', $this->retriever->retrieve(URL::fromString('http://kibo.test/local.file')));
        $this->assertEquals('local-test/local.file', $this->retrievedFile);

        $this->assertFalse($this->retriever->retrieve(URL::fromString('http://test.com/make.me')));
    }



}
