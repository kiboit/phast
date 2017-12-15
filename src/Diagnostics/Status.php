<?php

namespace Kibo\Phast\Diagnostics;

class Status implements \JsonSerializable {

    /**
     * @var string
     */
    private $featureName;

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
     * @param string $feature
     * @param bool $available
     * @param string $reason
     * @param bool $enabled
     */
    public function __construct($feature, $available, $reason, $enabled) {
        $this->featureName = $feature;
        $this->available = $available;
        $this->reason = $reason;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getFeatureName() {
        return $this->featureName;
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
            'featureName' => $this->featureName,
            'available' => $this->available,
            'reason' => $this->reason,
            'enabled' => $this->enabled
        ];
    }

    public function jsonSerialize() {
        return $this->toArray();
    }


}
