<?php


namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Services\ServiceRequest;

class BundlerParamsParser {
    public function parse(ServiceRequest $request) {
        $result = [];
        foreach ($request->getParams() as $name => $value) {
            if (strpos($name, '_') !== false) {
                list($name, $key) = explode('_', $name, 2);
                $result[$key][$name] = $value;
            }
        }
        return $result;
    }
}
