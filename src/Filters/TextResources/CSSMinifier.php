<?php

namespace Kibo\Phast\Filters\TextResources;

class CSSMinifier implements TextResourceFilter {


    /**
     * @param TextResource $resource
     * @return TextResource
     */
    public function transform(TextResource $resource) {
        // TODO: Extract the comments removal in separate filter
        // TODO: Ensure comments removal on all css processing (somehow)
        // Remove comments
        $content = preg_replace('~/\*[^*]*\*+([^/*][^*]*\*+)*/~', '', $resource->getContent());

        // Normalize whitespace
        $content = preg_replace('~\s+~', ' ', $content);

        // Remove whitespace before and after operators
        $chars = [',', '{', '}', ';'];
        foreach ($chars as $char) {
            $content = str_replace("$char ", $char, $content);
            $content = str_replace(" $char", $char, $content);
        }

        // Remove whitespace after colons
        $content = str_replace(': ', ':', $content);

        return $resource->modifyContent(trim($content));
    }

}
