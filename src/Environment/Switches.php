<?php

namespace Kibo\Phast\Environment;

class Switches {

    const SWITCH_PHAST = 'phast';

    const SWITCH_DIAGNOSTICS = 'diagnostics';

    private $switches = [
        'phast' => true,
        'diagnostics' => false
    ];

    private function __construct() {}

    public static function fromArray(array $switches) {
         $instance = new self();
         $instance->switches = array_merge($instance->switches, $switches);
         return $instance;
    }

    public static function fromString($switches) {
        $instance = new self();
        if (empty ($switches)) {
            return $instance;
        }
        foreach (explode('.', $switches) as $switch) {
            if ($switch[0] == '-') {
                $instance->switches[substr($switch, 1)] = false;
            } else {
                $instance->switches[$switch] = true;
            }
        }
        return $instance;
    }

    public function merge(Switches $switches) {
        $instance = new self();
        $instance->switches = array_merge($this->switches, $switches->switches);
        return $instance;
    }

    public function isOn($switch) {
        return !isset ($this->switches[$switch]) || $this->switches[$switch];
    }

    public function toArray() {
        return $this->switches;
    }

}
