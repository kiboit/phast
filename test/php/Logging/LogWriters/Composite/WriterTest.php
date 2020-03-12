<?php

namespace Kibo\Phast\Logging\LogWriters\Composite;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase {
    public function testCallingOthers() {
        $message = new LogEntry(LogLevel::ERROR, '', []);
        $writer = new Writer();
        for ($i = 0; $i < 10; $i++) {
            $otherWriter = $this->createMock(Writer::class);
            $otherWriter->expects($this->once())
                ->method('writeEntry')
                ->with($message);
            $writer->addWriter($otherWriter);
        }
        $writer->writeEntry($message);
    }
}
