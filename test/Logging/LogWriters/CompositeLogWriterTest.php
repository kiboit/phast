<?php

namespace Kibo\Phast\Logging\LogWriters;


use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriter;
use PHPUnit\Framework\TestCase;

class CompositeLogWriterTest extends TestCase {

    public function testCallingOthers() {
        $message = new LogEntry(LogLevel::ERROR, '', []);
        $writer = new CompositeLogWriter();
        for ($i = 0; $i < 10; $i++) {
            $otherWriter = $this->createMock(LogWriter::class);
            $otherWriter->expects($this->once())
                ->method('writeEntry')
                ->with($message);
            $writer->addWriter($otherWriter);
        }
        $writer->writeEntry($message);
    }

}
