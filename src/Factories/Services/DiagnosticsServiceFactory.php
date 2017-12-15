<?php


namespace Kibo\Phast\Factories\Services;


use Kibo\Phast\Logging\LogWriters\JSONLFile\Writer;
use Kibo\Phast\Services\DiagnosticsService;

class DiagnosticsServiceFactory {

    public function make(array $config) {
        $logRoot = null;
        foreach ($config['logging']['logWriters'] as $writerConfig) {
            if ($writerConfig['class'] == Writer::class) {
                $logRoot = $writerConfig['logRoot'];
                break;
            }
        }
        if (is_null($logRoot)) {
            throw new \RuntimeException('Could not find logging config');
        }
        return new DiagnosticsService($logRoot);
    }

}
