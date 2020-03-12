<?php

namespace Kibo\Phast\Environment;

class Switches {
    const SWITCH_PHAST = 'phast';

    const SWITCH_DIAGNOSTICS = 'diagnostics';

    private static $defaults = [
        self::SWITCH_PHAST => true,
        self::SWITCH_DIAGNOSTICS => false,
    ];

    private $switches = [];

    public static function fromArray(array $switches) {
        $instance = new self();
        $instance->switches = array_merge($instance->switches, $switches);
        return $instance;
    }

    public static function fromString($switches) {
        $instance = new self();
        if (empty($switches)) {
            return $instance;
        }
        foreach (explode(',', $switches) as $switch) {
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
        if (isset($this->switches[$switch])) {
            return (bool) $this->switches[$switch];
        }
        if (isset(self::$defaults[$switch])) {
            return (bool) self::$defaults[$switch];
        }
        return true;
    }

    public function toArray() {
        return array_merge(self::$defaults, $this->switches);
    }
}
