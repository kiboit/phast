<?php

namespace Kibo\Phast\Logging;

use Kibo\Phast\Common\ObjectifiedFunctions;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {
    /**
     * @var LogEntry
     */
    private $messageWritten;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $writer;

    /**
     * @var Logger
     */
    private $logger;

    public function setUp(): void {
        parent::setUp();
        $this->messageWritten = null;
        $this->writer = $this->createMock(LogWriter::class);
        $this->writer->method('writeEntry')
            ->willReturnCallback(function (LogEntry $message) {
                $this->messageWritten = $message;
            });
        $functions = new ObjectifiedFunctions();
        $functions->microtime = function () {
            return 1000;
        };
        $this->logger = new Logger($this->writer, $functions);
    }

    /**
     * @dataProvider getLevelTestData
     */
    public function testLevel($method, $level) {
        $theMessage = 'The message';
        $theContext = ['key' => 'value', 'timestamp' => 10];
        call_user_func([$this->logger, $method], $theMessage, $theContext);
        $this->assertInstanceOf(LogEntry::class, $this->messageWritten);
        $this->assertEquals($level, $this->messageWritten->getLevel());
        $this->assertEquals($theMessage, $this->messageWritten->getMessage());
        $this->assertEquals($theContext, $this->messageWritten->getContext());
    }

    public function getLevelTestData() {
        return [
            ['emergency', LogLevel::EMERGENCY],
            ['alert',     LogLevel::ALERT],
            ['critical',  LogLevel::CRITICAL],
            ['error',     LogLevel::ERROR],
            ['warning',   LogLevel::WARNING],
            ['notice',    LogLevel::NOTICE],
            ['info',      LogLevel::INFO],
            ['debug',     LogLevel::DEBUG],
        ];
    }

    public function testWithContext() {
        // test setting a default context and merging it with message context
        $logger = $this->logger->withContext(['k1' => 'v1']);
        $logger->debug('A message', ['mk1' => 'mv1', 'timestamp' => 10]);
        $this->assertEquals(['k1' => 'v1', 'mk1' => 'mv1', 'timestamp' => 10], $this->messageWritten->getContext());

        // test merging new default context with old default context
        $logger = $logger->withContext(['k2' => 'v2']);
        $logger->debug('A message', ['timestamp' => 10]);
        $this->assertEquals(['k1' => 'v1', 'k2' => 'v2', 'timestamp' => 10], $this->messageWritten->getContext());

        // test overriding contexts
        $logger = $logger->withContext(['k2' => 'v3']);
        $logger->debug('A message', ['k1' => 'mv3', 'timestamp' => 10]);
        $this->assertEquals(['k1' => 'mv3', 'k2' => 'v3', 'timestamp' => 10], $this->messageWritten->getContext());
    }

    public function testSettingTimestampInContext() {
        $this->logger->debug('A message');
        $context = $this->messageWritten->getContext();
        $this->assertEquals(1000, $context['timestamp']);
    }
}
