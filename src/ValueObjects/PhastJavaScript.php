<?php
namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Common\ObjectifiedFunctions;

class PhastJavaScript {
    /**
     * @var string
     */
    private $filename;

    /**
     * @var ?string
     */
    private $rawScript;

    /**
     * @var ?string
     */
    private $minifiedScript;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @var mixed
     */
    private $config;

    private function __construct(string $filename, string $script, bool $minified) {
        $this->filename = $filename;
        if ($minified) {
            $this->minifiedScript = $script;
        } else {
            $this->rawScript = $script;
        }
    }

    /**
     * @param string $filename
     * @param ObjectifiedFunctions|null $funcs
     */
    public static function fromFile($filename, ObjectifiedFunctions $funcs = null) {
        $funcs = $funcs ? $funcs : new ObjectifiedFunctions();
        $contents = $funcs->file_get_contents($filename);
        if ($contents === false) {
            throw new \RuntimeException("Could not read script: $filename");
        }
        return new self($filename, $contents, false);
    }

    /**
     * @param string $filename
     * @param string $contents
     */
    public static function fromString($filename, $contents) {
        return new self($filename, $contents, true);
    }

    /**
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * @return bool|string
     */
    public function getContents() {
        if ($this->minifiedScript === null) {
            $this->minifiedScript = (new JSMinifier($this->rawScript))->min();
        }
        return $this->minifiedScript;
    }

    /**
     * @return string
     */
    public function getCacheSalt() {
        $hash = md5($this->rawScript ?? $this->minifiedScript, true);
        return substr(base64_encode($hash), 0, 16);
    }

    /**
     * @param string $configKey
     * @param mixed $config
     */
    public function setConfig($configKey, $config) {
        $this->configKey = $configKey;
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function hasConfig() {
        return isset($this->configKey);
    }

    /**
     * @return string
     */
    public function getConfigKey() {
        return $this->configKey;
    }

    /**
     * @return mixed
     */
    public function getConfig() {
        return $this->config;
    }
}
