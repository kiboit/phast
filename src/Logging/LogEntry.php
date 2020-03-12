<?php

namespace Kibo\Phast\Logging;

class LogEntry implements \JsonSerializable {
    /**
     * @var int
     */
    private $level;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $context;

    /**
     * LogEntry constructor.
     * @param int $level
     * @param string $message
     * @param array $context
     */
    public function __construct($level, $message, array $context) {
        $this->level = (int) $level;
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getContext() {
        return $this->context;
    }

    public function toArray() {
        return [
            'level' => $this->level,
            'message' => $this->message,
            'context' => $this->context,
        ];
    }

    public function jsonSerialize() {
        return $this->toArray();
    }
}
