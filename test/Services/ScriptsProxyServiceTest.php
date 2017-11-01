<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Security\ServiceSignature;
use PHPUnit\Framework\TestCase;

class ScriptsProxyServiceTest extends TestCase {

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * @var ScriptsProxyService
     */
    private $service;

    public function setUp() {
        parent::setUp();
        $signature = $this->createMock(ServiceSignature::class);
        $signature->expects($this->once())
            ->method('verify')
            ->willReturn(true);
        $this->functions = new ObjectifiedFunctions();
        $this->service = new ScriptsProxyService($signature, $this->functions);
    }

    public function testFetching() {
        $request = [
            'src' => 'the-script',
            'token' => 'token'
        ];
        $this->functions->file_get_contents = function ($url) {
            $this->assertEquals('the-script', $url);
            return 'the-content';
        };
        $result = $this->service->serve($request);
        $this->assertEquals('the-content', $result);
    }

    public function testExceptionOnNoResult() {
        $request = ['src' => 'the-script', 'token' => 'token'];
        $this->functions->file_get_contents = function () {
            return false;
        };
        $this->expectException(ItemNotFoundException::class);
        $this->service->serve($request);
    }

}
