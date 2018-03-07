<?php

return call_user_func(function () {
    $all_caps = json_decode(file_get_contents(__DIR__ . '/browsers.json'), true);
    return $all_caps;
});
