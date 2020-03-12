<?php


namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\HTML\HTMLFilterFactory;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        $glue = strpos($config['servicesUrl'], '?') === false ? '?' : '&';
        $bundlerUrl = $config['servicesUrl'] . $glue . 'service=bundler';
        $cache = new Cache($config['cache'], 'phast-scripts');
        $compiler = new PhastJavaScriptCompiler($cache, $bundlerUrl);
        return new Filter($compiler);
    }
}
