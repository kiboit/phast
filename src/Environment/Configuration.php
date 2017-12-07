<?php

namespace Kibo\Phast\Environment;

use Kibo\Phast\Services\ServiceRequest;

class Configuration {

    /**
     * @var array
     */
    private $sourceConfig;

    /**
     * Configuration constructor.
     * @param array $sourceConfig
     */
    public function __construct(array $sourceConfig) {
        $this->sourceConfig = $sourceConfig;
        if (!isset ($this->sourceConfig['switches'])) {
            $this->sourceConfig['switches'] = [];
        }
        if (!isset ($this->sourceConfig['switches']['phast'])) {
            $this->sourceConfig['switches']['phast'] = true;
        }
    }

    public function withServiceRequest(ServiceRequest $request) {
        $clone = clone $this;
        $clone->sourceConfig['switches'] = array_merge(
            $clone->sourceConfig['switches'],
            $request->getSwitches()
        );
        return $clone;
    }


    public function toArray() {
        $config = $this->sourceConfig;
        $switchables = [
            &$config['documents']['filters'],
            &$config['images']['filters'],
            &$config['logging']['logWriters']
        ];
        $switches = $config['switches'];
        foreach ($switchables as &$switchable) {
            $switchable = array_filter($switchable, function ($item) use ($switches) {
                if (!isset ($item['enabled'])) {
                    return true;
                }
                if ($item['enabled'] === false) {
                    return false;
                }
                return !isset ($switches[$item['enabled']]) || $switches[$item['enabled']];
            });
        }
        return $config;
    }

}
