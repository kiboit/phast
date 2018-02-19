<?php

return call_user_func(function () {

    $html = file_get_contents(__DIR__ . '/../../test/test-app/example.php');
    $filter = new Kibo\Phast\Filters\HTML\ScriptsDeferring\Filter();
    $doc = new \Kibo\Phast\Common\DOMDocument(new \Kibo\Phast\Parsing\HTML\HTMLStream());
    $doc->loadHTML($html);

    return function () use ($doc, $filter) {
        $filter->transformHTMLDOM($doc);
    };
});
