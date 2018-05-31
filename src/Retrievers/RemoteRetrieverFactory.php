<?php

namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Exceptions\RuntimeException;

class RemoteRetrieverFactory {

    const CONFIG_KEY = 'httpClient';

    public function make(array $config) {
        $spec = $config[self::CONFIG_KEY];

        if (is_callable($spec)) {
            $client = $spec();
        } elseif (class_exists($spec)) {
            $client = new $spec;
        } else {
            throw new RuntimeException(self::CONFIG_KEY . " config value must be either callable or a class name");
        }

        return new RemoteRetriever($client);
    }

}