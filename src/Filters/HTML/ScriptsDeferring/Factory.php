<?php
namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        return new Filter($config['csp']);
    }
}
