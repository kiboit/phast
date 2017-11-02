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
    private $headers;

    private function __construct() {}

    public static function fromGlobals() {
        return self::fromArray($_GET, getallheaders());
    }

    public static function fromArray($get, $headers) {
        $instance = new self;
        $instance->get = $get;
        $instance->headers = $headers;
        return $instance;
    }

    /**
     * @return array
     */
    public function getGet() {
        return $this->get;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }
}
