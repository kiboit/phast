<?php
namespace Kibo\Phast\Logging\LogWriters\RotatingTextFile;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LogEntry;
use Kibo\Phast\Logging\LogLevel;
use Kibo\Phast\Logging\LogWriters\BaseLogWriter;

class Writer extends BaseLogWriter {
    /** @var string */
    private $path = 'phast.log';

    /** @var int */
    private $maxFiles = 2;

    /** @var int */
    private $maxSize = 10 * 1024 * 1024;

    /** @var ObjectifiedFunctions */
    private $funcs;

    /**
     * @param array $config
     * @param ?ObjectifiedFunctions $funcs
     */
    public function __construct(array $config, ObjectifiedFunctions $funcs = null) {
        if (isset($config['path'])) {
            $this->path = (string) $config['path'];
        }
        if (isset($config['maxFiles'])) {
            $this->maxFiles = (int) $config['maxFiles'];
        }
        if (isset($config['maxSize'])) {
            $this->maxSize = (int) $config['maxSize'];
        }
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    protected function doWriteEntry(LogEntry $entry) {
        if (!($this->levelMask & $entry->getLevel())) {
            return;
        }
        $message = $this->interpolate($entry->getMessage(), $entry->getContext());
        $line = sprintf(
            "%s %s %s\n",
            gmdate('Y-m-d\TH:i:s\Z', $this->funcs->time()),
            LogLevel::toString($entry->getLevel()),
            $message
        );
        clearstatcache(true, $this->path);
        $this->rotate(strlen($line));
        file_put_contents($this->path, $line, FILE_APPEND);
    }

    private function interpolate($message, $context) {
        $prefix = '';
        $prefixKeys = [
            'requestId',
            'service',
            'class',
            'method',
            'line',
        ];
        foreach ($prefixKeys as $key) {
            if (isset($context[$key])) {
                $prefix .= '{' . $key . "}\t";
            }
        }
        return preg_replace_callback('/{(.+?)}/', function ($match) use ($context) {
            return array_key_exists($match[1], $context) ? $context[$match[1]] : $match[0];
        }, $prefix . $message);
    }

    private function rotate($bufferSize) {
        if (!$this->shouldRotate($bufferSize)) {
            return;
        }
        if (!($fp = fopen($this->path, 'r+'))) {
            return;
        }
        try {
            if (!flock($fp, LOCK_EX | LOCK_NB)) {
                return;
            }
            if (!$this->shouldRotate($bufferSize)) {
                return;
            }
            for ($i = $this->maxFiles - 1; $i > 0; $i--) {
                @rename($this->getName($i - 1), $this->getName($i));
            }
        } finally {
            fclose($fp);
        }
    }

    private function getName($index) {
        if ($index <= 0) {
            return $this->path;
        }
        return $this->path . '.' . $index;
    }

    private function shouldRotate($bufferSize) {
        $currentSize = @filesize($this->path);
        if (!$currentSize) {
            return false;
        }
        $newSize = $currentSize + $bufferSize;
        return $newSize > $this->maxSize;
    }
}
