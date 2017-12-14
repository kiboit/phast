<?php

namespace Kibo\Phast\Environment;

use Kibo\Phast\Services\ServiceRequest;

class Configuration {

    /**
     * @var array
     */
    private $sourceConfig;

    /**
     * @var Switches
     */
    private $switches;

    /**
     * Configuration constructor.
     * @param array $sourceConfig
     */
    public function __construct(array $sourceConfig) {
        $this->sourceConfig = $sourceConfig;
        if (!isset ($this->sourceConfig['switches'])) {
            $this->switches = new Switches();
        } else {
            $this->switches = Switches::fromArray($this->sourceConfig['switches']);
        }
    }

    public function withServiceRequest(ServiceRequest $request) {
        $clone = clone $this;
        $clone->switches = $this->switches->merge($request->getSwitches());
        return $clone;
    }


    public function toArray() {
        $config = $this->sourceConfig;
        $switchables = [
            &$config['documents']['filters'],
            &$config['images']['filters'],
            &$config['logging']['logWriters']
        ];
        foreach ($switchables as &$switchable) {
            $switchable = array_filter($switchable, function ($item) {
                if (!isset ($item['enabled'])) {
                    return true;
                }
                if ($item['enabled'] === false) {
                    return false;
                }
                return $this->switches->isOn($item['enabled']);
            });
        }
        if (isset ($config['images']['enable-cache']) && is_string($config['images']['enable-cache'])) {
            $config['images']['enable-cache'] = $this->switches->isOn($config['images']['enable-cache']);
        }
        $config['switches'] = $this->switches->toArray();
        return $config;
    }

}
