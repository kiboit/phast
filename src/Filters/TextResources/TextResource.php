<?php


namespace Kibo\Phast\Filters\TextResources;


use Kibo\Phast\ValueObjects\URL;

class TextResource {

    /**
     * @var URL
     */
    private $location;

    /**
     * @var string
     */
    private $content;

    /**
     * TextResource constructor.
     * @param URL $location
     * @param string $contents
     */
    public function __construct(URL $location, $contents) {
        $this->location = $location;
        $this->content = $contents;
    }

    /**
     * @return URL
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $newContent
     * @return TextResource
     */
    public function modifyContent($newContent) {
        return new self($this->location, $newContent);
    }


}
