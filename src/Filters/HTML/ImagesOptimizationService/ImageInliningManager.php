<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;


use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\ValueObjects\URL;

class ImageInliningManager {

    private $ext2mime = [
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];

    /**
     * @var int
     */
    private $maxImageInliningSize;

    /**
     * @var LocalRetriever
     */
    private $retriever;

    /**
     * ImageInliningManager constructor.
     * @param int $maxImageInliningSize
     * @param LocalRetriever $retriever
     */
    public function __construct($maxImageInliningSize, LocalRetriever $retriever) {
        $this->maxImageInliningSize = $maxImageInliningSize;
        $this->retriever = $retriever;
    }

    public function canBeInlined(URL $url) {
        $size = $this->retriever->getSize($url);
        return $size !== false
            && $size < $this->maxImageInliningSize
            && $this->getMimeType($url);
    }

    public function toDataUrl(URL $url) {
        $mime = $this->getMimeType($url);
        $content = $this->retriever->retrieve($url);
        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    public function getMimeType(URL $url) {
        $ext = strtolower($url->getExtension());
        if (!isset ($this->ext2mime[$ext])) {
            return false;
        }
        return $this->ext2mime[$ext];
    }
}
