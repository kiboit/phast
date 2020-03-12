<?php


namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Services\ServiceRequest;

class ShortBundlerParamsParser {
    public static function getParamsMappings() {
        return [
            's' => 'src',
            'i' => 'strip-imports',
            'c' => 'cacheMarker',
            't' => 'token',
            'j' => 'isScript',
            'r' => 'ref',
        ];
    }

    public function parse(ServiceRequest $request) {
        $query = $request->getHTTPRequest()->getQueryString();
        if (preg_match('/(^|&)f=/', $query)) {
            $query = $this->unobfuscateQuery($query);
        }
        $query = $this->parseQuery($query);
        $query = $this->unshortenParams($query);
        $query = $this->uncompressSrcs($query);
        $result = [];
        $current = null;
        foreach ($query as $key => $value) {
            if (in_array($key, ['src', 'ref'])) {
                if ($current) {
                    $result[] = $current;
                }
                $current = [];
            }
            if ($current !== null) {
                $current[$key] = $value;
            }
        }
        if ($current) {
            $result[] = $current;
        }
        return $result;
    }

    private function unobfuscateQuery($query) {
        $query = str_rot13($query);
        if (strpos($query, '%2S') !== false) {
            $query = preg_replace_callback('/%../', function ($match) {
                return str_rot13($match[0]);
            }, $query);
        }
        return $query;
    }

    private function parseQuery($string) {
        $pieces = explode('&', $string);
        foreach ($pieces as $piece) {
            $element = explode('=', $piece, 2);
            $key = urldecode($element[0]);
            $value = isset($element[1]) ? urldecode($element[1]) : '';
            yield $key => $value;
        }
    }

    private function unshortenParams(\Generator $query) {
        $mappings = self::getParamsMappings();
        foreach ($query as $key => $value) {
            if (isset($mappings[$key])) {
                yield $mappings[$key] => $value === '' ? '1' : $value;
            } else {
                yield $key => $value;
            }
        }
    }

    private function uncompressSrcs(\Generator $query) {
        $lastUrl = '';
        foreach ($query as $key => $value) {
            if ($key === 'src') {
                $prefixLength = (int) base_convert(substr($value, 0, 2), 36, 10);
                $suffix = substr($value, 2);
                $value = substr($lastUrl, 0, $prefixLength) . $suffix;
                $lastUrl = $value;
            }
            yield $key => $value;
        }
    }
}
