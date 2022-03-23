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
     * @var string
     */
    private $contents;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @var mixed
     */
    private $config;

    /**
     * @var bool
     */
    private $minified;

    private function __construct(string $filename, string $contents, bool $minified) {
        $this->filename = $filename;
        $this->contents = $contents;
        $this->minified = $minified;
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
        if (!$this->minified) {
            $this->contents = (new JSMinifier($this->contents))->min();
            $this->minified = true;
        }
        return $this->contents;
    }

    /**
     * @return string
     */
    public function getCacheSalt() {
        $hash = md5($this->getContents(), true);
        return substr(preg_replace('/^[a-z0-9]/i', '', base64_encode($hash)), 0, 16);
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
