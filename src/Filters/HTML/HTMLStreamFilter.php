<?php


namespace Kibo\Phast\Filters\HTML;


interface HTMLStreamFilter {

    /**
     * @param HTMLPageContext $context
     * @param \Traversable $elements
     * @return \Traversable
     */
    public function transformElements(HTMLPageContext $context, \Traversable $elements);

}
