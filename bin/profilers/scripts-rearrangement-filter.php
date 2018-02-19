<?php

return call_user_func(function () use ($iterations) {

    $html = file_get_contents(__DIR__ . '/../../test/test-app/webcarpet.php');
    $filter = new Kibo\Phast\Filters\HTML\ScriptsRearrangement\Filter();

    for ($i = 0; $i < $iterations; $i++) {
        $doc = new \Kibo\Phast\Common\DOMDocument(new \Kibo\Phast\Parsing\HTML\HTMLStream());
        $doc->loadHTML($html);
        $documents[] = $doc;
    }

    return function () use (&$documents, $filter) {
        $doc = array_pop($documents);
        $filter->transformHTMLDOM($doc);
    };
});
