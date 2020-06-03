<?php


namespace Kibo\Phast\Filters\CSS\FontSwap;

use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {
    const FONT_FACE_REGEXP = '/(@font-face\s*\{)([^}]*)/i';

    const ICON_FONT_FAMILIES = [
        'Font Awesome',
        'GeneratePress',
        'Dashicons',
        'Ionicons',
    ];

    private $fontDisplayBlockPattern;

    public function __construct() {
        $this->fontDisplayBlockPattern = $this->getFontDisplayBlockPattern();
    }

    public function apply(Resource $resource, array $request) {
        $css = $resource->getContent();
        $filtered = preg_replace_callback(self::FONT_FACE_REGEXP, function ($match) {
            list($block, $start, $contents) = $match;
            $mode = preg_match($this->fontDisplayBlockPattern, $contents) ? 'block' : 'swap';
            return $start . 'font-display:' . $mode . ';' . $contents;
        }, $css);
        return $resource->withContent($filtered);
    }

    private function getFontDisplayBlockPattern() {
        $patterns = [];
        foreach (self::ICON_FONT_FAMILIES as $family) {
            $chars = str_split($family);
            $chars = array_map(function ($char) {
                return preg_quote($char, '~');
            }, $chars);
            $patterns[] = implode('\s*', $chars);
        }
        return '~' . implode('|', $patterns) . '~i';
    }
}
