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
     * @return Configuration
     */
    public static function fromDefaults() {
        return new self(DefaultConfiguration::get());
    }

    /**
     * Configuration constructor.
     * @param array $sourceConfig
     */
    public function __construct(array $sourceConfig) {
        $this->sourceConfig = $sourceConfig;
        if (!isset($this->sourceConfig['switches'])) {
            $this->switches = new Switches();
        } else {
            $this->switches = Switches::fromArray($this->sourceConfig['switches']);
        }
    }

    /**
     * @param Configuration $config
     * @return $this
     */
    public function withUserConfiguration(Configuration $config) {
        $result = $this->recursiveMerge($this->sourceConfig, $config->sourceConfig);
        return new self($result);
    }

    public function withServiceRequest(ServiceRequest $request) {
        $clone = clone $this;
        $clone->switches = $this->switches->merge($request->getSwitches());
        return $clone;
    }

    public function getRuntimeConfig() {
        $config = $this->sourceConfig;
        $switchables = [
            &$config['documents']['filters'],
            &$config['images']['filters'],
            &$config['logging']['logWriters'],
            &$config['styles']['filters'],
        ];
        foreach ($switchables as &$switchable) {
            if (!is_array($switchable)) {
                continue;
            }
            $switchable = array_filter($switchable, function ($item) {
                if (!isset($item['enabled'])) {
                    return true;
                }
                if ($item['enabled'] === false) {
                    return false;
                }
                return $this->switches->isOn($item['enabled']);
            });
        }
        if (isset($config['images']['enable-cache']) && is_string($config['images']['enable-cache'])) {
            $config['images']['enable-cache'] = $this->switches->isOn($config['images']['enable-cache']);
        }
        $config['switches'] = $this->switches->toArray();
        return new Configuration($config);
    }

    public function toArray() {
        return $this->sourceConfig;
    }

    private function recursiveMerge(array $a1, array $a2) {
        foreach ($a2 as $key => $value) {
            if (isset($a1[$key]) && is_array($a1[$key]) && is_array($value)) {
                $a1[$key] = $this->recursiveMerge($a1[$key], $value);
            } elseif (is_string($key)) {
                $a1[$key] = $value;
            } else {
                $a1[] = $value;
            }
        }
        return $a1;
    }
}
