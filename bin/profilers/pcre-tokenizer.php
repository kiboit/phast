<?php

use Kibo\Phast\Parsing\HTML\PCRETokenizer;

return call_user_func(function () {
    $data = file_get_contents(__DIR__ . '/../../test/test-app/example.php');

    return function () use ($data) {
        $tok = new PCRETokenizer();
        $tokens = iterator_to_array($tok->tokenize($data));
    };
});
