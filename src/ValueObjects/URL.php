<?php

namespace Kibo\Phast\ValueObjects;

class URL {

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    /**
     * @param $string
     * @return URL
     */
    public static function fromString($string) {
        return self::fromArray(parse_url($string));
    }

    /**
     * @param array $arr
     * @return URL
     */
    public static function fromArray(array $arr) {
        $url = new self();
        foreach ($arr as $key => $value) {
            $url->$key = $value;
        }
        return $url;
    }

    /**
     * @param URL $base
     * @return URL
     */
    public function withBase(URL $base) {
        $classVars = get_class_vars(self::class);
        unset ($classVars['query']);
        unset ($classVars['fragment']);

        $fromBase = [];
        foreach (array_keys($classVars) as $key) {
            if ($key == 'path') {
                $fromBase['path'] = $this->resolvePath($base->path, $this->path);
            } else if (!isset ($this->$key) && isset ($base->$key)) {
                $fromBase[$key] = $base->$key;
            } elseif (isset ($this->$key)) {
                break;
            }
        }

        $cleaned = array_filter(get_object_vars($this));

        return self::fromArray(array_merge($cleaned, $fromBase));
    }

    private function resolvePath($base, $requested) {
        if (!$requested) {
            return $base;
        }
        if ($requested[0] == '/') {
            return $requested;
        }
        if (substr($base, -1, 1) == '/') {
            $usedBase = $base;
        } else {
            $usedBase = dirname($base);
        }
        return rtrim($usedBase, '/') . '/' . $requested;
    }

    /**
     * @return string
     */
    public function toString() {
        $scheme   = isset($this->scheme) ? $this->scheme . '://' : '';
        $host     = isset($this->host) ? $this->host : '';
        $port     = isset($this->port) ? ':' . $this->port : '';
        $user     = isset($this->user) ? $this->user : '';
        $pass     = isset($this->pass) ? ':' . $this->pass  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($this->path) ? $this->path : '';
        $query    = isset($this->query) ? '?' . $this->query : '';
        $fragment = isset($this->fragment) ? '#' . $this->fragment : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * @return string
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment() {
        return $this->fragment;
    }

    public function __toString() {
        return $this->toString();
    }

}
