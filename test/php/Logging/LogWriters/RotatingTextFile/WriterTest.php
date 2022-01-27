<?php
namespace Kibo\Phast\Logging\LogWriters\RotatingTextFile;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Services\ServiceRequest;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase {
    private $tempDir;

    public function setUp(): void {
        $this->tempDir = sys_get_temp_dir() . '/phast-rotating-text-file-test.' . uniqid();
        mkdir($this->tempDir);
    }

    public function tearDown(): void {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($this->tempDir);
    }

    public function testWriteLog() {
        $funcs = new ObjectifiedFunctions();
        $funcs->time = function () {
            return strtotime('2010-05-06 12:34:56 UTC');
        };
        $writer = new Writer([
            'path' => $this->tempDir . '/test.log',
            'maxFiles' => 3,
            'maxSize' => 41 * 2,
        ], $funcs);
        for ($i = 0; $i < 7; $i++) {
            $writer->writeEntry(new LogEntry(LogLevel::DEBUG, 'Hello, {who}!', ['who' => 'World']));
        }
        $this->assertEquals(
            str_repeat("2010-05-06T12:34:56Z DEBUG Hello, World!\n", 1),
            file_get_contents($this->tempDir . '/test.log')
        );
        $this->assertEquals(
            str_repeat("2010-05-06T12:34:56Z DEBUG Hello, World!\n", 2),
            file_get_contents($this->tempDir . '/test.log.1')
        );
        $this->assertEquals(
            str_repeat("2010-05-06T12:34:56Z DEBUG Hello, World!\n", 2),
            file_get_contents($this->tempDir . '/test.log.2')
        );
        $this->assertFalse(file_exists($this->tempDir . '/test.log.3'));
    }

    public function testFactory() {
        $factory = new Factory();
        $writer = $factory->make(['path' => 'test'], new ServiceRequest());

        $this->assertInstanceOf(Writer::class, $writer);

        $class = new \ReflectionClass($writer);
        $prop = $class->getProperty('path');
        $prop->setAccessible(true);
        $this->assertEquals('test', $prop->getValue($writer));
    }
}
