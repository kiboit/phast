<?php
namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Filters\HTML\HTMLFilterFactory;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        $cache = new Cache($config['cache'], 'phast-scripts');
        $compiler = new PhastJavaScriptCompiler(
            $cache,
            $config['servicesUrl'],
            $config['serviceRequestFormat']
        );
        return new Filter($compiler, $config['csp']['nonce']);
    }
}
