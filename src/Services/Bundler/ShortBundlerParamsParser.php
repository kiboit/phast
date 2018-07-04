<?php


namespace Kibo\Phast\Services\Bundler;


use Kibo\Phast\Services\ServiceRequest;

class ShortBundlerParamsParser {

    const PARAM_MAPPINGS = [
        's' => 'src',
        'i' => 'strip-imports',
        'c' => 'cacheMarker',
        't' => 'token'
    ];

    public function parse(ServiceRequest $request) {
        $query = $request->getHTTPRequest()->getEnvValue('QUERY_STRING');
        $result = [];
        foreach (preg_split('/&(?=s=)/', $query) as $part) {
            $result[] = $this->parseAndMap($part);
        }
        return $result;
    }

    private function parseAndMap($string) {
        $parsed = [];
        parse_str($string, $parsed);
        $mapped = [];
        foreach ($parsed as $key => $value) {
            if (isset (self::PARAM_MAPPINGS[$key])) {
                $mapped[self::PARAM_MAPPINGS[$key]] = $value === '' ? '1' : $value;
            } else {
                $mapped[$key] = $value;
            }
        }
        return $mapped;
    }

}
