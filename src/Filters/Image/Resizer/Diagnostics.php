<?php


namespace Kibo\Phast\Filters\Image\Resizer;


use Kibo\Phast\Filters\Image\CommonDiagnostics\BaseDiagnostics;

class Diagnostics extends BaseDiagnostics {

    public function getTestRequest() {
        return ['width' => 10, 'height' => 10];
    }

}
