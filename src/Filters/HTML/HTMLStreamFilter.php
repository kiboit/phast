<?php


namespace Kibo\Phast\Filters\HTML;


interface HTMLStreamFilter {

    /**
     * @param HTMLPageContext $context
     * @return \Traversable
     */
    public function transformElements(HTMLPageContext $context);

}
