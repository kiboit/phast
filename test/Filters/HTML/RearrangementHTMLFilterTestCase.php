<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Parsing\HTML\HTMLStream;

class RearrangementHTMLFilterTestCase extends HTMLFilterTestCase {

    /**
     * @var HTMLFilter
     */
    protected $filter;

    public function testExceptionOnNoBody() {
        $this->expectException(\Exception::class);
        $this->dom->setStream(new HTMLStream());
        $this->filter->transformHTMLDOM($this->dom);
    }

}
