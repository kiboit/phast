<?php


namespace Kibo\Phast\Filters\Image\ImageAPIClient;

use Kibo\Phast\Diagnostics\Diagnostics as DiagnosticsInterface;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Exceptions\RuntimeException;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;

class Diagnostics implements DiagnosticsInterface {
    public function diagnose(array $config) {
        $package = Package::fromPackageClass(get_class($this));
        /** @var ImageFilter $filter */
        $filter = $package->getFactory()->make($config);
        $imageData = @file_get_contents(__DIR__ . '/../CommonDiagnostics/kibo-logo.png');
        if ($imageData === false) {
            throw new RuntimeException('Could not read testing image for ' . static::class . ' diagnostics.');
        }
        $image = new DummyImage();
        $image->setImageString($imageData);
        $filter->transformImage($image, []);
    }
}
