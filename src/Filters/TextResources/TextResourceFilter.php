<?php


namespace Kibo\Phast\Filters\TextResources;

interface TextResourceFilter {

    /**
     * @param TextResource $resource
     * @return TextResource
     */
    public function transform(TextResource $resource);

}
