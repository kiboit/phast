<?php
namespace Kibo\Phast\Filters\HTML\MinifyScripts;

use Kibo\Phast\Cache\Sqlite\Cache;

class Factory {
    public function make(array $config) {
        return new Filter(new Cache($config['cache'], 'minified-inline-scripts'));
    }
}
