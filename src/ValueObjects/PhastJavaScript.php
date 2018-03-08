<?php


namespace Kibo\Phast\ValueObjects;


use Kibo\Phast\Common\ObjectifiedFunctions;

class PhastJavaScript {

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @var mixed
     */
    private $config;

    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    /**
     * PhastJavaScript constructor.
     * @param string $filename
     * @param ObjectifiedFunctions $funcs
     */
    public function __construct($filename, ObjectifiedFunctions $funcs = null) {
        $this->filename = $filename;
        $this->funcs = $funcs ? $funcs : new ObjectifiedFunctions();
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
        return $this->funcs->file_get_contents($this->filename);
    }

    /**
     * @return int
     */
    public function getCacheSalt() {
        return $this->funcs->filemtime($this->filename);
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
        return isset ($this->configKey);
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
