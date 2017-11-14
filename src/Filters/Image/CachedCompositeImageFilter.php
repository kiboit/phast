<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\URL;

class CachedCompositeImageFilter extends CompositeImageFilter {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Retriever
     */
    private $retriever;

    /**
     * @var array
     */
    private $request;

    /**
     * @var array
     */
    private $filtersNames = [];

    /**
     * CachedCompositeImageFilter constructor.
     *
     * @param Cache $cache
     * @param Retriever $retriever
     * @param array $request
     */
    public function __construct(Cache $cache, Retriever $retriever, array $request) {
        $this->cache = $cache;
        $this->retriever = $retriever;
        $this->request = $request;
    }

    /**
     * @param ImageFilter $filter
     */
    public function addImageFilter(ImageFilter $filter) {
        parent::addImageFilter($filter);
        $this->filtersNames[] = get_class($filter);
    }

    /**
     * @param Image $image
     * @return Image
     * @throws \Exception
     */
    public function apply(Image $image) {
        $url = URL::fromString($this->request['src']);
        $lastModTime = $this->retriever->getLastModificationTime($url);
        sort($this->filtersNames);
        $key = array_merge([$lastModTime, $this->request['src']], $this->filtersNames);
        if (isset ($this->request['width'])) {
            $key[] = $this->request['width'];
        }
        if (isset ($this->request['height'])) {
            $key[] = $this->request['height'];
        }
        if (isset ($this->request['preferredType'])) {
            $key[] = $this->request['preferredType'];
        }
        $key = implode("\n", $key);
        $filtered = $this->cache->get($key, function () use ($image) {
            try {
                return parent::apply($image);
            } catch (\Exception $e) {
                return $e;
            }
        });
        if ($filtered instanceof \Exception) {
            throw $filtered;
        }
        return $filtered;
    }

}
