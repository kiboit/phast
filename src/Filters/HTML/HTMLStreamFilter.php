<?php


namespace Kibo\Phast\Filters\HTML;

interface HTMLStreamFilter {
    /**
     * @param \Traversable $elements
     * @param HTMLPageContext $context
     * @return \Traversable
     */
    public function transformElements(\Traversable $elements, HTMLPageContext $context);
}
