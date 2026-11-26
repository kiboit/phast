<?php

namespace Kibo\Phast\Logging\LogWriters\JSONLFile;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase {
    /**
     * @dataProvider getWriteMessageTestData
     */
    public function testWriteMessage($suffix) {
        $dir = sys_get_temp_dir() . '/phast-test';

        $fileSuffix = preg_replace('/[^0-9A-Za-z_-]/', '', (string) $suffix);
        if (!empty($fileSuffix)) {
            $fileSuffix = '-' . $fileSuffix;
        }
        $filename = $dir . '/log' . $fileSuffix . '.jsonl';

        @unlink($filename);

        $writer = new Writer($dir, $suffix);
        $message = new LogEntry(LogLevel::DEBUG, 'The message', ['key' => 'value']);
        $writer->writeEntry($message);

        $written = file_get_contents($filename);
        $this->assertStringEndsWith("\n", $written);
        $decoded = json_decode(trim($written), true);
        $this->assertEquals(LogLevel::DEBUG, $decoded['level']);
        $this->assertEquals('The message', $decoded['message']);
        $this->assertEquals(['key' => 'value'], $decoded['context']);
    }

    public function getWriteMessageTestData() {
        return [[null], ['123'], ['../../123zxcZXC-_']];
    }

    private $dir;

    public function setUp(): void {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/phast-test-writer-' . getmypid();
        @mkdir($this->dir, 0700);
    }

    public function tearDown(): void {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        @rmdir($this->dir);
        parent::tearDown();
    }

    public function testCreatesFileWithMode0600() {
        $this->write('m1');
        $this->write('m2');
        $this->assertSame(0600, fileperms("$this->dir/log-id.jsonl") & 07777);
        $this->assertSame(['m1', 'm2'], $this->readMessages("$this->dir/log-id.jsonl"));
    }

    public function testDoesNotWriteThroughSymlink() {
        file_put_contents("$this->dir/target", "original\n");
        chmod("$this->dir/target", 0600);
        symlink("$this->dir/target", "$this->dir/log-id.jsonl");
        $this->write('m1');
        $this->assertSame("original\n", file_get_contents("$this->dir/target"));
    }

    public function testDoesNotWriteThroughHardLink() {
        file_put_contents("$this->dir/target", "original\n");
        chmod("$this->dir/target", 0600);
        link("$this->dir/target", "$this->dir/log-id.jsonl");
        $this->write('m1');
        $this->assertSame("original\n", file_get_contents("$this->dir/target"));
    }

    public function testDoesNotWriteToFileWithOtherMode() {
        file_put_contents("$this->dir/log-id.jsonl", "original\n");
        chmod("$this->dir/log-id.jsonl", 0644);
        $this->write('m1');
        $this->assertSame("original\n", file_get_contents("$this->dir/log-id.jsonl"));
    }

    private function write($message) {
        (new Writer($this->dir, 'id'))->writeEntry(new LogEntry(LogLevel::DEBUG, $message, []));
    }

    private function readMessages($filename) {
        return array_map(function ($line) {
            return json_decode($line, true)['message'];
        }, file($filename, FILE_IGNORE_NEW_LINES));
    }
}
