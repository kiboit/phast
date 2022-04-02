<?php

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Filters\HTML\CSSInlining\Optimizer;

return call_user_func(function () {
    $css = file_get_contents(__DIR__ . '/../../test/resources/large.css');

    $mocker = new PHPUnit_Framework_MockObject_Generator();
    $cache = $mocker->getMock(Cache::class, [], [], '', false);

    $optimizer = new Optimizer(new \ArrayIterator([]), $cache);

    return Closure::bind(function () use ($css) {
        $this->parseCSS($css);
    }, $optimizer, $optimizer);
});
