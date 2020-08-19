<?php
namespace Kibo\Phast\HTTP;

use Kibo\Phast\ValueObjects\Query;

class Request {
    /**
     * @var array|null
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

    /**
     * @var string
     */
    private $query;

    private function __construct() {
    }

    public static function fromGlobals() {
        return self::fromArray(null, $_SERVER, $_COOKIE);
    }

    public static function fromArray(array $get = null, array $env = [], array $cookie = []) {
        $instance = new self();
        $instance->get = $get;
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
        if ($this->get) {
            return Query::fromAssoc($this->get);
        }
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
        $script = $this->getEnvValue('PHP_SELF');
        $uri = $this->getEnvValue('DOCUMENT_URI');
        if ($script !== null
            && $uri !== null
            && strpos($uri, $script . '/') === 0
        ) {
            return substr($uri, strlen($script));
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

    private function getEnvValue($key) {
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
