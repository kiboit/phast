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

    /**
     * @var array
     */
    private $cookie;

    private function __construct() {}

    public static function fromGlobals() {
        $get = [];
        $parsed = parse_url($_SERVER['REQUEST_URI']);
        if (isset ($parsed['query'])) {
            parse_str($parsed['query'], $get);
        }
        return self::fromArray($get, $_SERVER, $_COOKIE);
    }

    public static function fromArray(array $get = [], array $env = [], array $cookie = []) {
        $instance = new self;
        $instance->get = $get;
        $instance->env = $env;
        $instance->cookie = $cookie;
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

        return $this->getEnvValue($key);
    }

    public function getPathInfo() {
        return $this->getEnvValue('PATH_INFO');
    }

    public function getCookie($name) {
        if (isset ($this->cookie[$name])) {
            return $this->cookie[$name];
        }
    }

    public function getEnvValue($key) {
        if (isset ($this->env[$key])) {
            return $this->env[$key];
        }
    }

}
