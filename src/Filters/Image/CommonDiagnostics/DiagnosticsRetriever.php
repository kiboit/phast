<?php


namespace Kibo\Phast\Filters\Image\CommonDiagnostics;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class DiagnosticsRetriever implements Retriever {
    /**
     * @var string
     */
    private $file;

    /**
     * DiagnosticsRetriever constructor.
     * @param string $file
     */
    public function __construct($file) {
        $this->file = $file;
    }

    public function retrieve(URL $url) {
        return file_get_contents($this->file);
    }

    public function getCacheSalt(URL $url) {
        return '';
    }
}
