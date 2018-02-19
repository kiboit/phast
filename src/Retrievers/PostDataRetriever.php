<?php


namespace Kibo\Phast\Retrievers;


use Kibo\Phast\ValueObjects\URL;

class PostDataRetriever implements Retriever {

    public function retrieve(URL $url) {
        return file_get_contents('php://input');
    }

    public function getLastModificationTime(URL $url) {
        return false;
    }


}
