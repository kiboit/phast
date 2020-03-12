<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Environment\Switches;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ServiceRequest {
    const FORMAT_QUERY = 1;

    const FORMAT_PATH  = 2;

    private static $defaultSerializationMode = self::FORMAT_PATH;

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
    private static $documentRequestId;

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
        if (!isset(self::$switches)) {
            self::$switches = new Switches();
        }
    }

    public static function resetRequestState() {
        self::$defaultSerializationMode = self::FORMAT_PATH;
        self::$propagatedSwitches = '';
        self::$switches = null;
        self::$documentRequestId = null;
    }

    public static function setDefaultSerializationMode($mode) {
        self::$defaultSerializationMode = $mode;
    }

    public static function fromHTTPRequest(Request $request) {
        $params = $request->getGet();
        if (isset($params['src'])) {
            $params['src'] = preg_replace('~^hxxp(?=s?://)~', 'http', $params['src']);
        }
        $pathInfo = $request->getPathInfo();
        if ($pathInfo) {
            $params = array_merge($params, self::parsePathInfo($pathInfo));
        }
        $instance = new self();
        self::$switches = new Switches();
        $instance->httpRequest = $request;
        if ($request->getCookie('phast')) {
            self::$switches = Switches::fromString($request->getCookie('phast'));
        }
        if (isset($params['token'])) {
            $instance->token = $params['token'];
            unset($params['token']);
        }
        $instance->params = $params;
        if (isset($params['phast'])) {
            self::$propagatedSwitches = $params['phast'];
            $paramsSwitches = Switches::fromString($params['phast']);
            self::$switches = self::$switches->merge($paramsSwitches);
        }
        if (isset($params['documentRequestId'])) {
            self::$documentRequestId = $params['documentRequestId'];
        } else {
            self::$documentRequestId = (string) mt_rand(0, 999999999);
        }
        return $instance;
    }

    public function hasRequestSwitchesSet() {
        return !empty(self::$propagatedSwitches);
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
    public function getDocumentRequestId() {
        return self::$documentRequestId;
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
            if (isset($pair[1])) {
                $values[$pair[0]] = self::decodeSingleValue($pair[1]);
            } elseif (!preg_match('/^__p__\./', $pair[0])) {
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

    public function serialize($format = null) {
        $params = $this->getAllParams();
        if ($this->token) {
            $params['token'] = $this->token;
        }
        if (is_null($format)) {
            $format = self::$defaultSerializationMode;
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
        if (!empty(self::$propagatedSwitches)) {
            $params['phast'] = self::$propagatedSwitches;
        }
        if (self::$switches->isOn(Switches::SWITCH_DIAGNOSTICS)) {
            $params['documentRequestId'] = self::$documentRequestId;
        }
        return $params;
    }

    private function serializeToQueryFormat(array $params) {
        $encoded = http_build_query($params);
        if (!isset($this->url)) {
            return $encoded;
        }
        $serialized = preg_replace('~\?.*~', '', (string) $this->url);
        if (self::$defaultSerializationMode === self::FORMAT_PATH
            && !preg_match('~/$~', $serialized)
        ) {
            $serialized .= '/' . $this->getDummyFilename($params);
        }
        return $serialized . '?' . $encoded;
    }

    /** @return string */
    private function serializeToPathFormat(array $params) {
        $encodedSrc = null;
        $values = [];
        foreach (explode('&', http_build_query($params)) as $element) {
            list($key, $value) = explode('=', $element, 2);
            $encodedValue = str_replace(['-', '%'], ['%2D', '-'], $value);
            if ($key == 'src') {
                $encodedSrc = $encodedValue;
            } else {
                $values[] = $key . '=' . $encodedValue;
            }
        }
        if ($encodedSrc) {
            array_unshift($values, $encodedSrc);
        }
        $params = '/' . join('/', $values) . '/' . $this->getDummyFilename($params);
        if (isset($this->url)) {
            return preg_replace(['~\?.*~', '~/$~'], '', $this->url) . $params;
        }
        return $params;
    }

    private function getDummyFilename(array $params) {
        return '__p__.' . $this->getDummyExtension($params);
    }

    private function getDummyExtension(array $params) {
        $default = 'js';
        if (empty($params['src'])) {
            return $default;
        }
        $url = URL::fromString($params['src']);
        $ext = strtolower($url->getExtension());
        if (preg_match('/^(jpe?g|gif|png|js|css)$/', $ext)) {
            return $ext;
        }
        return $default;
    }
}
