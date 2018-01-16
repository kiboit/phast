<?php


namespace Kibo\Phast\Filters\Image\JPEGTransEnhancer;



use Kibo\Phast\Filters\Image\CommonDiagnostics\BaseDiagnostics;

class Diagnostics extends BaseDiagnostics {

    protected function getTestFileName() {
        return __DIR__ . '/../CommonDiagnostics/kibo-logo.jpg';
    }

}
