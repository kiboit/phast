<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Environment\Switches;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ServiceRequest {

    const FORMAT_QUERY = 1;

    const FORMAT_PATH  = 2;

    /**
     * @var string
     */
    private static $propagatedSwitches = '';

    /**
     * @var Switches
     */
    private static $switches;

    /**
     * @var string
     */
    private static $requestId;

    /**
     * @var Request
     */
    private $httpRequest;

    /**
     * @var URL
     */
    private $url;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var string
     */
    private $token;

    public function __construct() {
        if (!isset (self::$switches)) {
            self::$switches = new Switches();
        }
    }

    public static function fromHTTPRequest(Request $request) {
        $params = $request->getGet();
        $pathInfo = $request->getPathInfo();
        if ($pathInfo) {
            $params = array_merge($params, self::parsePathInfo($pathInfo));
        }
        $instance = new self();
        self::$switches = new Switches();
        $instance->httpRequest = $request;
        if ($request->getCookie('switches')) {
            self::$switches = Switches::fromString($request->getCookie('switches'));
        }
        if (isset ($params['token'])) {
            $instance->token = $params['token'];
            unset ($params['token']);
        }
        $instance->params = $params;
        if (isset ($params['switches'])) {
            self::$propagatedSwitches = $params['switches'];
            $paramsSwitches = Switches::fromString($params['switches']);
            self::$switches = self::$switches->merge($paramsSwitches);
        }
        if (isset ($params['requestId'])) {
            self::$requestId = $params['requestId'];
        } else {
            self::$requestId = (string)mt_rand(0, 999999999);
        }
        return $instance;
    }

    /**
     * @return Switches
     */
    public function getSwitches() {
        return self::$switches;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @return Request
     */
    public function getHTTPRequest() {
        return $this->httpRequest;
    }

    /**
     * @return string
     */
    public function getRequestId() {
        return self::$requestId;
    }

    /**
     * @param array $params
     * @return ServiceRequest
     */
    public function withParams(array $params) {
        return $this->cloneWithProperty('params', $params);
    }

    /**
     * @param URL $url
     * @return ServiceRequest
     */
    public function withUrl(URL $url) {
        return $this->cloneWithProperty('url', $url);
    }

    /**
     * @param ServiceSignature $signature
     * @return ServiceRequest
     */
    public function sign(ServiceSignature $signature) {
        $token = $signature->sign($this->getVerificationString());
        return $this->cloneWithProperty('token', $token);
    }

    /**
     * @param ServiceSignature $signature
     * @return bool
     */
    public function verify(ServiceSignature $signature) {
        return $signature->verify($this->token, $this->getVerificationString());
    }

    private static function parsePathInfo($string) {
        $values = [];
        $parts = explode('/', preg_replace('~^/~', '', $string));
        foreach ($parts as $part) {
            $pair = explode('=', $part);
            if (isset ($pair[1])) {
                $values[$pair[0]] = self::decodeSingleValue($pair[1]);
            } else {
                $values['src'] = self::decodeSingleValue($pair[0]);
            }
        }
        return $values;
    }

    private static function decodeSingleValue($value) {
        return urldecode(str_replace('-', '%', $value));
    }

    private function getVerificationString() {
        $params = $this->getAllParams();
        ksort($params);
        return http_build_query($params);
    }

    private function cloneWithProperty($property, $value) {
        $clone = clone $this;
        $clone->$property = $value;
        return $clone;
    }

    public function serialize($format = self::FORMAT_QUERY) {
        $params = $this->getAllParams();
        if ($this->token) {
            $params['token'] = $this->token;
        }
        if ($format == self::FORMAT_PATH) {
            return $this->serializeToPathFormat($params);
        }
        return $this->serializeToQueryFormat($params);
    }

    private function getAllParams() {
        $urlParams = [];
        if ($this->url) {
            parse_str($this->url->getQuery(), $urlParams);
        }
        $params = array_merge($urlParams, $this->params);
        if (!empty (self::$propagatedSwitches)) {
            $params['switches'] = self::$propagatedSwitches;
        }
        if (self::$switches->isOn(Switches::SWITCH_DIAGNOSTICS)) {
            $params['requestId'] = self::$requestId;
        }
        return $params;
    }

    private function serializeToQueryFormat(array $params) {
        $encoded = http_build_query($params);
        if (isset ($this->url)) {
            return preg_replace('~\?.*~', '', (string)$this->url) . '?' . $encoded;
        }
        return $encoded;
    }

    private function serializeToPathFormat(array $params) {
        $srcValue = null;
        $values = [];
        foreach ($params as $key => $value) {
            $encodedValue = str_replace(['-', '%'], ['%2D', '-'], urlencode($value));
            if ($key == 'src') {
                $srcValue = $encodedValue;
            } else {
                $values[] = $key . '=' . $encodedValue;
            }
        }
        if ($srcValue) {
            array_unshift($values, $srcValue);
        }
        $params = '/' . join('/', $values);
        if (isset ($this->url)) {
            return preg_replace(['~\?.*~', '~/$~'], '', $this->url) . $params;
        }
        return $params;
    }
}
