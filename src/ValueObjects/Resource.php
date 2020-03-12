<?php


namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Retrievers\Retriever;

class Resource {
    private $ext2mime = [
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
    ];

    /**
     * @var URL
     */
    private $url;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var Resource[]
     */
    private $dependencies = [];

    private function __construct() {
    }

    public static function makeWithContent(URL $url, $content, $mimeType = null, $encoding = 'identity') {
        $instance = new self();
        $instance->url = $url;
        $instance->mimeType = $mimeType;
        $instance->content = $content;
        $instance->encoding = $encoding;
        return $instance;
    }

    public static function makeWithRetriever(URL $url, Retriever $retriever, $mimeType = null, $encoding = 'identity') {
        $instance = new self();
        $instance->url = $url;
        $instance->mimeType = $mimeType;
        $instance->retriever = $retriever;
        $instance->encoding = $encoding;
        return $instance;
    }

    /**
     * @return URL
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return string
     * @throws ItemNotFoundException
     */
    public function getContent() {
        if (!isset($this->content)) {
            $this->content = $this->retriever->retrieve($this->url);
            if ($this->content === false) {
                throw new ItemNotFoundException("Could not get {$this->url}");
            }
        }
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getMimeType() {
        if (!isset($this->mimeType)) {
            $ext = strtolower($this->url->getExtension());
            if (isset($this->ext2mime[$ext])) {
                $this->mimeType = $this->ext2mime[$ext];
            }
        }
        return $this->mimeType;
    }

    /**
     * @return bool|int
     */
    public function getSize() {
        if (isset($this->retriever) && method_exists($this->retriever, 'getSize')) {
            return $this->retriever->getSize($this->url);
        }
        if (isset($this->content)) {
            return strlen($this->content);
        }
        return false;
    }

    public function toDataURL() {
        $mime = $this->getMimeType();
        $content = $this->getContent();
        return "data:$mime;base64," . base64_encode($content);
    }

    /**
     * @return string
     */
    public function getEncoding() {
        return $this->encoding;
    }

    /**
     * @return Resource[]
     */
    public function getDependencies() {
        return $this->dependencies;
    }

    /**
     * @return bool|int
     */
    public function getCacheSalt() {
        return isset($this->retriever) ? $this->retriever->getCacheSalt($this->url) : 0;
    }

    /**
     * @param $content
     * @param null $mimeType
     * @param null $encoding
     * @return Resource
     */
    public function withContent($content, $mimeType = null, $encoding = null) {
        $new = clone $this;
        $new->content = $content;
        if (!is_null($mimeType)) {
            $new->mimeType = $mimeType;
        }
        if (!is_null($encoding)) {
            $new->encoding = $encoding;
        }
        return $new;
    }

    /**
     * @param Resource[] $dependencies
     * @return Resource
     */
    public function withDependencies(array $dependencies) {
        $new = clone $this;
        $new->dependencies = $dependencies;
        return $new;
    }
}
