<?php

namespace Kibo\Phast\Logging\LogWriters\PHPError;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase {
    public function testParamsFromConfig() {
        $functions = new ObjectifiedFunctions();
        $args = [];
        $functions->error_log = function () use (&$args) {
            $args = func_get_args();
        };
        $config = ['messageType' => 1, 'destination' => 'm@example.com', 'extraHeaders' => 'extra'];
        $writer = new Writer($config, $functions);
        $writer->writeEntry(new LogEntry(LogLevel::DEBUG, 'the-message', []));
        $this->assertCount(4, $args);
        $this->assertEquals('the-message', $args[0]);
        $this->assertEquals(1, $args[1]);
        $this->assertEquals('m@example.com', $args[2]);
        $this->assertEquals('extra', $args[3]);
    }

    public function testMessageFormatting() {
        $functions = new ObjectifiedFunctions();
        $actualMessage = null;
        $functions->error_log = function ($message) use (&$actualMessage) {
            $actualMessage = $message;
        };
        $writer = new Writer([], $functions);

        $message = 'The message with {param} here {nothing}';
        $writer->writeEntry(new LogEntry(LogLevel::DEBUG, $message, ['param' => 'value {test}', 'test' => 'nope']));
        $this->assertEquals('The message with value {test} here {nothing}', $actualMessage);


        $context = [
            'timestamp' => 100,
            'requestId' => 'ID',
            'service'   => 'the-service',
            'class'     => 'the-class',
            'method'    => 'the-method',
            'line'      => 20,
            'param'     => 'v2',
        ];
        $writer->writeEntry(new LogEntry(LogLevel::DEBUG, $message, $context));
        $this->assertEquals(
            "ID\tthe-service\tthe-class\tthe-method\t20\tThe message with v2 here {nothing}",
            $actualMessage
        );
    }
}
