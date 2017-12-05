<?php

namespace Kibo\Phast\Filters\HTML;

class RearrangementHTMLFilterTestCase extends HTMLFilterTestCase {

    /**
     * @var HTMLFilter
     */
    protected $filter;

    public function testExceptionOnNoBody() {
        $this->expectException(\Exception::class);
        $dom = new \Kibo\Phast\Common\DOMDocument();
        $this->filter->transformHTMLDOM($dom);
    }

}
