<?php


namespace Kibo\Phast\Filters\Image\ImageAPIClient;

use Kibo\Phast\Diagnostics\Diagnostics as DiagnosticsInterface;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Filters\Image\ImageFilter;
use Kibo\Phast\Filters\Image\ImageImplementations\GDImage;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetrieverFactory;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\ValueObjects\URL;

class Diagnostics implements  DiagnosticsInterface {

    public function diagnose(array $config) {
        $package = Package::fromPackageClass(get_class($this));
        /** @var ImageFilter $filter */
        $filter = $package->getFactory()->make($config);
        $url = $config['images']['filters'][Filter::class]['diagnostics-image-url'];
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever((new RemoteRetrieverFactory())->make($config));
        $image = new GDImage(URL::fromString($url), $retriever);
        $filter->transformImage($image, ['src' => $url]);
    }


}
