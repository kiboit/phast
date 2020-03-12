<?php

namespace Kibo\Phast\Diagnostics;

use Kibo\Phast\Environment\Package;

class Status implements \JsonSerializable {
    /**
     * @var Package
     */
    private $package;

    /**
     * @var bool
     */
    private $available;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Status constructor.
     * @param Package $package
     * @param bool $available
     * @param string $reason
     * @param bool $enabled
     */
    public function __construct(Package $package, $available, $reason, $enabled) {
        $this->package = $package;
        $this->available = $available;
        $this->reason = $reason;
        $this->enabled = $enabled;
    }

    /**
     * @return Package
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     * @return bool
     */
    public function isAvailable() {
        return $this->available;
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'package' => [
                'type' => $this->package->getType(),
                'name' => $this->package->getNamespace(),
            ],
            'available' => $this->available,
            'reason' => $this->reason,
            'enabled' => $this->enabled,
        ];
    }

    public function jsonSerialize() {
        return $this->toArray();
    }
}
