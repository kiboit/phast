<?php


namespace Kibo\Phast\Services\Bundler;


use Kibo\Phast\Services\ServiceRequest;

class ShortBundlerParamsParser {

    public static function getParamsMappings() {
        return [
            's' => 'src',
            'i' => 'strip-imports',
            'c' => 'cacheMarker',
            't' => 'token'
        ];
    }

    public function parse(ServiceRequest $request) {
        $query = $request->getHTTPRequest()->getEnvValue('QUERY_STRING');
        $result = [];
        foreach (preg_split('/&(?=s=)/', $query) as $part) {
            $parsed = $this->parseAndMap($part);
            if (isset ($parsed['src'])) {
                $result[] = $parsed;
            }
        }
        return empty ($result) ? [$parsed] : $result;
    }

    private function parseAndMap($string) {
        $parsed = [];
        parse_str($string, $parsed);
        $mappings = self::getParamsMappings();
        $mapped = [];
        foreach ($parsed as $key => $value) {
            if (isset ($mappings[$key])) {
                $mapped[$mappings[$key]] = $value === '' ? '1' : $value;
            } else {
                $mapped[$key] = $value;
            }
        }
        return $mapped;
    }

}
