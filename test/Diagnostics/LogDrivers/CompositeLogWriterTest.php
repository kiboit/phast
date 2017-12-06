<?php

namespace Kibo\Phast\Diagnostics\LogDrivers;


use Kibo\Phast\Diagnostics\LogEntry;
use Kibo\Phast\Diagnostics\LogLevel;
use Kibo\Phast\Diagnostics\LogWriter;
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
