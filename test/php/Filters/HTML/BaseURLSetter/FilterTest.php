<?php

namespace Kibo\Phast\Filters\HTML\BaseURLSetter;

use Kibo\Phast\Filters\HTML\HTMLFilterTestCase;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

class FilterTest extends HTMLFilterTestCase {
    public function testBaseURLSetting() {
        $checkPoint1 = $this->makeMarkedElement('meta');
        $checkPoint1->setAttribute('data-expected-base', self::BASE_URL);
        $this->head->appendChild($checkPoint1);

        $baseWithTarget = $this->makeMarkedElement('base');
        $baseWithTarget->setAttribute('target', '_self');
        $this->head->appendChild($baseWithTarget);

        $checkPoint2 = $this->makeMarkedElement('meta');
        $checkPoint2->setAttribute('data-expected-base', self::BASE_URL);
        $this->head->appendChild($checkPoint2);

        $baseWithHref = $this->makeMarkedElement('base');
        $baseWithHref->setAttribute('href', '/dir/');
        $this->head->appendChild($baseWithHref);

        $checkPoint3 = $this->makeMarkedElement('meta');
        $checkPoint3->setAttribute('data-expected-base', self::BASE_URL . '/dir/');
        $this->head->appendChild($checkPoint3);

        $this->filter = $this->createMock(HTMLStreamFilter::class);
        $this->filter->expects($this->once())
            ->method('transformElements')
            ->willReturnCallback(function (\Traversable $elements, HTMLPageContext $context) {
                /** @var Tag $element */
                foreach ($elements as $element) {
                    if ($element instanceof Tag && $element->hasAttribute('data-expected-base')) {
                        $this->assertEquals(
                            $element->getAttribute('data-expected-base'),
                            $context->getBaseUrl()
                        );
                    }
                    yield $element;
                }
            });

        $this->applyFilter();
    }
}
