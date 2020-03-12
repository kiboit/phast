<?php

namespace Kibo\Phast\Logging;

trait LoggingTrait {
    protected function logger($method = null, $line = null) {
        $context = ['class' => get_class($this)];
        if (!is_null($method)) {
            $context['method'] = $method;
        }
        if (!is_null($line)) {
            $context['line'] = $line;
        }
        return Log::context($context);
    }
}
