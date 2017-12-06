<?php

namespace Kibo\Phast\Diagnostics;


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

    public function setUp() {
        parent::setUp();
        $this->messageWritten = null;
        $this->writer = $this->createMock(LogWriter::class);
        $this->writer->method('writeEntry')
            ->willReturnCallback(function (LogEntry $message) {
                $this->messageWritten = $message;
            });
        $this->logger = new Logger($this->writer);
    }

    /**
     * @dataProvider getLevelTestData
     */
    public function testLevel($method, $level) {
        $theMessage = 'The message';
        $theContext = ['key' => 'value'];
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
            ['debug',     LogLevel::DEBUG]
        ];
    }

    public function testWithContext() {
        // test setting a default context and merging it with message context
        $logger = $this->logger->withContext(['k1' => 'v1']);
        $logger->debug('A message', ['mk1' => 'mv1']);
        $this->assertEquals(['k1' => 'v1', 'mk1' => 'mv1'], $this->messageWritten->getContext());

        // test merging new default context with old default context
        $logger = $logger->withContext(['k2' => 'v2']);
        $logger->debug('A message');
        $this->assertEquals(['k1' => 'v1', 'k2' => 'v2'], $this->messageWritten->getContext());

        // test overriding contexts
        $logger = $logger->withContext(['k2' => 'v3']);
        $logger->debug('A message', ['k1' => 'mv3']);
        $this->assertEquals(['k1' => 'mv3', 'k2' => 'v3'], $this->messageWritten->getContext());
    }
}
