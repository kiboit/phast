<?php
namespace Kibo\Phast\HTTP;

use Kibo\Phast\ValueObjects\Query;
use Kibo\Phast\ValueObjects\URL;

class Request {
    /**
     * @var array
     */
    private $env;

    /**
     * @var array
     */
    private $cookie;

    /**
     * @var string
     */
    private $query;

    private function __construct() {
    }

    public static function fromGlobals() {
        $instance = new self();
        $instance->env = $_SERVER;
        $instance->cookie = $_COOKIE;
        return $instance;
    }

    public static function fromArray(array $get = [], array $env = [], array $cookie = []) {
        if ($get) {
            $url = isset($env['REQUEST_URI']) ? $env['REQUEST_URI'] : '';
            $env['REQUEST_URI'] = URL::fromString($url)->withQuery(http_build_query($get))->toString();
        }
        $instance = new self();
        $instance->env = $env;
        $instance->cookie = $cookie;
        return $instance;
    }

    /**
     * @return array
     */
    public function getGet() {
        return $this->getQuery()->toAssoc();
    }

    /**
     * @return Query
     */
    public function getQuery() {
        return Query::fromString($this->getQueryString());
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
        $pathInfo = $this->getEnvValue('PATH_INFO');
        if ($pathInfo) {
            return $pathInfo;
        }
        $path = parse_url($this->getEnvValue('REQUEST_URI'), PHP_URL_PATH);
        if (preg_match('~[^/]\.php(/.*)~', $path, $match)) {
            return $match[1];
        }
    }

    public function getCookie($name) {
        if (isset($this->cookie[$name])) {
            return $this->cookie[$name];
        }
    }

    public function getQueryString() {
        $parsed = parse_url($this->getEnvValue('REQUEST_URI'));
        if (isset($parsed['query'])) {
            return $parsed['query'];
        }
    }

    public function getAbsoluteURI() {
        return
            ($this->getEnvValue('HTTPS') ? 'https' : 'http') . '://' .
            $this->getHost() . $this->getURI();
    }

    public function getHost() {
        return $this->getHeader('Host');
    }

    public function getURI() {
        return $this->getEnvValue('REQUEST_URI');
    }

    public function getEnvValue($key) {
        if (isset($this->env[$key])) {
            return $this->env[$key];
        }
    }

    public function getDocumentRoot() {
        $scriptName = (string) $this->getEnvValue('SCRIPT_NAME');
        $scriptFilename = $this->normalizePath((string) $this->getEnvValue('SCRIPT_FILENAME'));

        if (strpos($scriptName, '/') === 0
            && $this->isAbsolutePath($scriptFilename)
            && $this->isSuffix($scriptName, $scriptFilename)
        ) {
            return substr($scriptFilename, 0, strlen($scriptFilename) - strlen($scriptName));
        }

        return $this->getEnvValue('DOCUMENT_ROOT');
    }

    private function normalizePath($path) {
        return str_replace('\\', '/', $path);
    }

    private function isAbsolutePath($path) {
        return preg_match('~^/|^[a-z]:/~i', $path);
    }

    private function isSuffix($suffix, $string) {
        return substr($string, -strlen($suffix)) === $suffix;
    }

    public function isCloudflare() {
        return !!$this->getHeader('CF-Ray');
    }
}
