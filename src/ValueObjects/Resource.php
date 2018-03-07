<?php


namespace Kibo\Phast\ValueObjects;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Retrievers\Retriever;

class Resource {

    /**
     * @var URL
     */
    private $url;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var string
     */
    private $content;

    /**
     * @var Resource[]
     */
    private $dependencies = [];

    private function __construct() {}

    public static function makeWithContent(URL $url, $content, $mimeType = null) {
        $instance = new self();
        $instance->url = $url;
        $instance->mimeType = $mimeType;
        $instance->content = $content;
        return $instance;
    }

    public static function makeWithRetriever(URL $url, Retriever $retriever, $mimeType = null) {
        $instance = new self();
        $instance->url = $url;
        $instance->mimeType = $mimeType;
        $instance->retriever = $retriever;
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
     */
    public function getMimeType() {
        return $this->mimeType;
    }

    /**
     * @return string
     * @throws ItemNotFoundException
     */
    public function getContent() {
        if (!isset ($this->content)) {
            $this->content = $this->retriever->retrieve($this->url);
            if ($this->content === false) {
                throw new ItemNotFoundException("Could not get {$this->url}");
            }
        }
        return $this->content;
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
    public function getLastModificationTime() {
        return isset ($this->retriever) ? $this->retriever->getLastModificationTime($this->url) : 0;
    }

    /**
     * @param $content
     * @param string|null $mimeType
     * @return Resource
     */
    public function withContent($content, $mimeType = null) {
        $new = clone $this;
        $new->content = $content;
        if (!is_null($mimeType)) {
            $new->mimeType = $mimeType;
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
