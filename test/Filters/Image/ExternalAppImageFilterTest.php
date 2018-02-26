<?php

namespace Kibo\Phast\Filters\Image;


use Kibo\Phast\Filters\Image\Exceptions\ImageProcessingException;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use PHPUnit\Framework\TestCase;

class ExternalAppImageFilterTest extends TestCase {

    /**
     * @var bool
     */
    private $shouldApply;

    /**
     * @var string
     */
    private $cmdpath;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $imageContent;

    public function setUp() {
        parent::setUp();
        $this->shouldApply = true;
        $this->cmdpath = '/usr/local/bin/php';
        $this->command = $this->cmdpath . ' -r "echo \"called\";"';
        $this->imageContent = 'test-image';

    }

    public function testExecution() {
        $this->assertEquals('called', $this->performTest()->getAsString());
    }

    public function testNotCallingWhenShouldNotApply() {
        $this->shouldApply = false;
        $image = $this->performTest();
        $this->assertEquals($this->imageContent, $image->getAsString());
    }

    public function testExceptionOnNonExistingExecutable() {
        $this->cmdpath = 'does-not-exist';
        $this->expectException(ImageProcessingException::class);
        $this->expectExceptionMessageRegExp('/^Executable not found/');
        $this->performTest();
    }

    public function testExceptionOnBadExitValue() {
        $this->command = $this->cmdpath . ' -r "exit(-1);"';
        $this->expectException(ImageProcessingException::class);
        $this->expectExceptionMessageRegExp('/^External image processing command failed with status/');
        $this->performTest();
    }

    public function testExceptionOnNoResult() {
        $this->command = $this->cmdpath . ' -r "echo \"\";"';
        $this->expectException(ImageProcessingException::class);
        $this->expectExceptionMessageRegExp('/^External image processing command did not output anything/');
        $this->performTest();
    }

    /**
     * @return DummyImage
     */
    private function performTest() {
        $filter = $this->getMockBuilder(ExternalAppImageFilter::class)
            ->setConstructorArgs([['cmdpath' => $this->cmdpath]])
            ->setMethods(['shouldApply', 'getCommand'])
            ->getMockForAbstractClass();
        $filter->method('getCommand')
            ->willReturn($this->command);
        $filter->method('shouldApply')
            ->willReturn($this->shouldApply);
        $image = new DummyImage();
        $image->setImageString($this->imageContent);
        return $filter->transformImage($image, []);
    }



}
