<?php


namespace Kibo\Phast\Filters\Image\CommonDiagnostics;

use Kibo\Phast\Diagnostics\Diagnostics;
use Kibo\Phast\Diagnostics\DiagnosticsRetriever;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Filters\Image\ImageImplementations\GDImage;
use Kibo\Phast\ValueObjects\URL;

abstract class BaseDiagnostics implements Diagnostics {

    protected function getTestFileName() {
        return __DIR__ . '/kibo-logo.png';
    }

    protected function getTestRequest() {
        return [];
    }

    public function diagnose(array $config) {
        $package = Package::fromPackageClass(get_class($this));
        $factory = $package->getFactory();
        /** @var ImageFilter $filter */
        $filter = (new $factory())->make($config);
        $image = new GDImage(
            URL::fromString('/some/where'),
            new DiagnosticsRetriever($this->getTestFileName())
        );
        $filter->transformImage($image, $this->getTestRequest());
    }
}
