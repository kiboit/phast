<?php


namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Diagnostics\Diagnostics as DiagnosticsInterface;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Logging\Logger;

class Diagnostics implements DiagnosticsInterface {
    public function diagnose(array $config) {
        Log::setLogger(new Logger(new DiagnosticsLogWriter()));
        $cache = new Cache($config['cache'], 'cache-self-diagnosis');
        $v1 = $cache->get('test-key', function () {
            return 1;
        }, 2);
        $v2 = $cache->get('test-key', function () {
            return 2;
        }, 2);
        if ($v1 != $v2) {
            throw new RuntimeException('Cache failed, but no error was reported!');
        }
    }
}
