<?php

namespace Kibo\Phast\Logging\LogReaders\JSONLFile;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogWriters\JSONLFile\Writer;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase {
    public function testReading() {
        $dir = sys_get_temp_dir();
        $file = 'json-l-reader-test';
        $writer = new Writer($dir, $file);
        $writer->writeEntry(new LogEntry(1, 'm1', ['k1' => 'v1']));
        $writer->writeEntry(new LogEntry(2, 'm2', ['k2' => 'v2']));

        $reader = new Reader($dir, $file);
        $actual = [];
        foreach ($reader->readEntries() as $entry) {
            $actual[] = $entry->toArray();
        }

        $expected = [
            [
                'level' => 1,
                'message' => 'm1',
                'context' => ['k1' => 'v1'],
            ],
            [
                'level' => 2,
                'message' => 'm2',
                'context' => ['k2' => 'v2'],
            ],
        ];
        $this->assertEquals($expected, $actual);
        $this->assertFileDoesNotExist("$dir/log-$file.jsonl");
    }

    public function testCleaningOldLogs() {
        $dir = sys_get_temp_dir();
        for ($i = 0; $i < 10; $i++) {
            touch("$dir/i$i.jsonl", time() - 610);
        }
        touch("$dir/old.xml", time() - 610);
        touch("$dir/new.jsonl");

        new Reader($dir, 'a');

        $this->assertFileExists("$dir/old.xml");
        $this->assertFileExists("$dir/new.jsonl");
        for ($i = 0; $i < 10; $i++) {
            $this->assertFileDoesNotExist("$dir/$i.jsonl");
        }
    }

    public static function tearDownAfterClass(): void {
        $dir = sys_get_temp_dir();
        @unlink("$dir/log-json-l-reader-test.jsonl");
        @unlink("$dir/new.jsonl");
        @unlink("$dir/old.xml");
        for ($i = 0; $i < 10; $i++) {
            @unlink("$dir/i$i.jsonl");
        }
    }
}
