<?php
namespace Kibo\Phast\Filters\Text\Decode;

use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {
    /** @dataProvider decodeData */
    public function testDecode($input) {
        $result = (new Filter())
            ->apply(Resource::makeWithContent(URL::fromString(''), $input))
            ->getContent();

        $this->assertEquals($result, 'Hello, World!');
    }

    public function decodeData() {
        yield ['Hello, World!'];
        yield ["\xef\xbb\xbfHello, World!"];
    }
}
