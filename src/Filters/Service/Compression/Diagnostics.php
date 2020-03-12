<?php


namespace Kibo\Phast\Filters\Service\Compression;

use Kibo\Phast\Cache\File\DiagnosticsLogWriter;
use Kibo\Phast\Logging\Log;
use Kibo\Phast\Logging\Logger;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class Diagnostics implements \Kibo\Phast\Diagnostics\Diagnostics {
    public function diagnose(array $config) {
        Log::setLogger(new Logger(new DiagnosticsLogWriter()));
        $resource = Resource::makeWithContent(URL::fromString('http://phast.test'), 'the-content');
        $compressed = (new CompressingFilter())->apply($resource, []);
        (new DecompressingFilter())->apply($compressed, []);
    }
}
