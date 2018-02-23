<?php

namespace Kibo\Phast\HTTP;

class Response {

    /**
     * @var int
     */
    private $code = 200;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var string
     */
    private $content;

    /**
     * @return int
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        $headers = $this->headers;

        if (!isset($headers['ETag'])) {
            $headers['ETag'] = $this->generateETag();
        }

        return $headers;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    private function generateETag() {
        return md5(http_build_query($this->headers) . "\0" . $this->content);
    }

}
