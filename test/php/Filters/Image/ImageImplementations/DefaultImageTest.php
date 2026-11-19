<?php

namespace Kibo\Phast\Filters\Image\ImageImplementations;

use Kibo\Phast\Exceptions\ClientErrorException;
use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class DefaultImageTest extends TestCase {
    /**
     * @dataProvider invalidImages
     */
    public function testInvalidImageIsAClientError($data, $message) {
        $retriever = $this->createMock(Retriever::class);
        $retriever->method('retrieve')->willReturn($data);
        $image = new DefaultImage(URL::fromString('http://phast.test/image.png'), $retriever);

        try {
            $image->getWidth();
            $this->fail('Expected an exception');
        } catch (ClientErrorException $e) {
            $this->assertInstanceOf(ImageProcessingException::class, $e);
            $this->assertSame($message, $e->getMessage());
        }
    }

    public function invalidImages() {
        return [
            'empty' => ['', 'Image is empty'],
            'not an image' => ['<html>not an image</html>', 'Could not read GD image info'],
        ];
    }
}
