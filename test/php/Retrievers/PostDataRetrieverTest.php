<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\PhastTestCase;
use Kibo\Phast\ValueObjects\URL;

class PostDataRetrieverTest extends PhastTestCase {
    public function testRetriever() {
        $funcs =  new ObjectifiedFunctions();

        $openedFile = null;
        $funcs->file_get_contents = function ($file) use (&$openedFile) {
            $openedFile = $file;
            return 'the-content';
        };

        $retriever = new PostDataRetriever($funcs);
        $url = URL::fromString(self::BASE_URL);
        $actualContent = $retriever->retrieve($url);
        $actualSalt = $retriever->getCacheSalt($url);

        $this->assertEquals('the-content', $actualContent);
        $this->assertNotEmpty($actualSalt);
        $this->assertEquals('php://input', $openedFile);

        $openedFile = false;
        $this->assertEquals($actualContent, $retriever->retrieve($url));
        $this->assertEquals($actualSalt, $retriever->getCacheSalt($url));
        $this->assertFalse($openedFile);
    }
}
