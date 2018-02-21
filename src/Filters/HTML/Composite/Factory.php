<?php

namespace Kibo\Phast\Filters\HTML\Composite;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Common\PhastJavaScriptCompiler;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\ValueObjects\URL;

class Factory {

    public function make(array $config) {
        $cache = new Cache($config['cache'], 'phast-scripts');
        $compiler = new PhastJavaScriptCompiler($cache);
        $composite = new Filter(
            $config['documents']['maxBufferSizeToApply'],
            URL::fromString($config['documents']['baseUrl']),
            $compiler
        );
        foreach (array_keys($config['documents']['filters']) as $class) {
            $package = Package::fromPackageClass($class);
            if ($package->hasFactory()) {
                $filter = $package->getFactory()->make($config);
            } else {
                $filter = new $class();
            }
            $composite->addHTMLFilter($filter);
        }
        return $composite;
    }

}
