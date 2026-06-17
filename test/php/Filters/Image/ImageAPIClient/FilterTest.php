<?php

namespace Kibo\Phast\Filters\Image\ImageAPIClient;

use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\HTTP\Client;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase {
    public function testSendingAllPreferredTypesInAcceptHeader() {
        $signature = $this->createMock(ServiceSignature::class);
        $signature->method('sign')->willReturn('token');

        $response = new Response();
        $response->setContent('optimized-image');
        $response->setHeader('Content-Type', Image::TYPE_AVIF);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('post')
            ->with(
                $this->isInstanceOf(URL::class),
                'original-image',
                $this->callback(function ($headers) {
                    $this->assertSame(Image::TYPE_AVIF . ',' . Image::TYPE_WEBP, $headers['Accept']);
                    return true;
                })
            )
            ->willReturn($response);

        $image = new DummyImage();
        $image->setImageString('original-image');
        $image->setType(Image::TYPE_JPEG);

        $filter = new Filter([
            'api-url' => 'http://optimize.test/?service=images',
            'host-name' => 'example.test',
            'request-uri' => '/page',
            'plugin-version' => 'test',
        ], $signature, $client);
        $result = $filter->transformImage($image, [
            'preferredType' => Image::TYPE_AVIF . ',' . Image::TYPE_WEBP,
        ]);

        $this->assertSame('optimized-image', $result->getAsString());
        $this->assertSame(Image::TYPE_AVIF, $result->getType());
    }
}
