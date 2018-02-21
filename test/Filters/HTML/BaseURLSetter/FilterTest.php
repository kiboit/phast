<?php

namespace Kibo\Phast\Filters\HTML\BaseURLSetter;


use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\URL;

class FilterTest extends \PHPUnit_Framework_TestCase {

    public function testBaseURLSetting() {
        $original = 'http://phast.test';
        $context = new HTMLPageContext(URL::fromString($original));
        $elements = [
            new Tag('html'),
            new Tag('head'),
            new Tag('base', ['target' => '_self']),
            new Tag('base', ['href' => '/dir/']),
            new Tag('body')
        ];

        $transformed = (new Filter())->transformElements(new \ArrayIterator($elements), $context);

        $transformed->current();
        $this->assertEquals($original, $context->getBaseUrl());
        $transformed->next();
        $this->assertEquals($original, $context->getBaseUrl());
        $transformed->next();
        $this->assertEquals($original, $context->getBaseUrl());

        $expected = $original . '/dir/';
        $transformed->next();
        $this->assertEquals($expected, $context->getBaseUrl());
        $transformed->next();
        $this->assertEquals($expected, $context->getBaseUrl());


    }

}
