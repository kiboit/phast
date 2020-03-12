<?php

namespace Kibo\Phast\Logging\LogWriters;

use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use PHPUnit\Framework\TestCase;

class BaseLogWriterTest extends TestCase {
    public function testLogMasking() {
        $levels = 0;
        $writer = $this->getMockForAbstractClass(BaseLogWriter::class);
        $writer->expects($this->exactly(2))
            ->method('doWriteEntry')
            ->willReturnCallback(function (LogEntry $entry) use (&$levels) {
                $levels |= $entry->getLevel();
            });
        $mask = LogLevel::INFO | LogLevel::ERROR;
        $writer->setLevelMask($mask);
        for ($level = LogLevel::DEBUG; $level <= LogLevel::EMERGENCY; $level = $level << 1) {
            $writer->writeEntry(new LogEntry($level, '', []));
        }
        $this->assertEquals($mask, $levels);
    }
}
