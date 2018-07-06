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
            $parsed = $this->map($this->parseQuery($part));
            if (isset ($parsed['src'])) {
                $result[] = $parsed;
            }
        }
        return empty ($result) ? [$parsed] : $this->uncompressSrcs($result);
    }

    private function parseQuery($string) {
        $parsed = [];
        parse_str($string, $parsed);
        return $parsed;
    }

    private function map($parsed) {
        $mapped = [];
        $mappings = self::getParamsMappings();
        foreach ($parsed as $key => $value) {
            if (isset ($mappings[$key])) {
                $mapped[$mappings[$key]] = $value === '' ? '1' : $value;
            } else {
                $mapped[$key] = $value;
            }
        }
        return $mapped;
    }

    private function uncompressSrcs(array $params) {
        $lastUrl = '';
        foreach ($params as &$item) {
            $src = $item['src'];
            $prefixLength = base_convert(substr($src, 0, 2), 36, 10);
            $suffix = substr($src, 2);
            $item['src'] = substr($lastUrl, 0, $prefixLength) . $suffix;
            $lastUrl = $item['src'];
        }
        return $params;
    }

}
