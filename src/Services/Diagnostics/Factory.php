<?php


namespace Kibo\Phast\Services\Diagnostics;

use Kibo\Phast\Logging\LogWriters\JSONLFile\Writer;

class Factory {
    public function make(array $config) {
        $logRoot = null;
        foreach ($config['logging']['logWriters'] as $writerConfig) {
            if ($writerConfig['class'] == Writer::class) {
                $logRoot = $writerConfig['logRoot'];
                break;
            }
        }
        return new Service($logRoot);
    }
}
