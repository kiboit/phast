<?php

namespace Kibo\Phast\HTTP;

class Request {

    /**
     * @var array
     */
    private $get;

    /**
     * @var array
     */
    private $env;

    private function __construct() {}

    public static function fromGlobals() {
        return self::fromArray($_GET, $_SERVER);
    }

    public static function fromArray($get, $env) {
        $instance = new self;
        $instance->get = $get;
        $instance->env = $env;
        return $instance;
    }

    /**
     * @return array
     */
    public function getGet() {
        return $this->get;
    }

    /**
     * @param $name string
     * @return string|null
     */
    public function getHeader($name) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        if (isset($this->env[$key])) {
            return $this->env[$key];
        }
    }

}
