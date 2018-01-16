<?php


namespace Kibo\Phast\Filters\Image\WEBPEncoder;


use Kibo\Phast\Filters\Image\CommonDiagnostics\BaseDiagnostics;
use Kibo\Phast\Filters\Image\Image;

class Diagnostics extends BaseDiagnostics {

    public function getTestFileName() {
        return __DIR__ . '/../CommonDiagnostics/kibo-logo.jpg';
    }

    public function getTestRequest() {
        return ['preferredType' => Image::TYPE_WEBP];
    }

}
