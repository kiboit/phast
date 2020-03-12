<?php


namespace Kibo\Phast\Retrievers;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\ValueObjects\URL;

class PostDataRetriever implements Retriever {
    /**
     * @var ObjectifiedFunctions
     */
    private $funcs;

    private $content;

    /**
     * PostDataRetriever constructor.
     * @param ObjectifiedFunctions $funcs
     */
    public function __construct(ObjectifiedFunctions $funcs = null) {
        $this->funcs = is_null($funcs) ? new ObjectifiedFunctions() : $funcs;
    }

    public function retrieve(URL $url) {
        if (!isset($this->content)) {
            $this->content = $this->funcs->file_get_contents('php://input');
        }
        return $this->content;
    }

    public function getCacheSalt(URL $url) {
        return md5($this->retrieve($url));
    }
}
