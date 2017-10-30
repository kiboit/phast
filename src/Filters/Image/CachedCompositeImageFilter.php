<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Cache\Cache;

class CachedCompositeImageFilter extends CompositeImageFilter {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $request;

    private $filtersNames = [];

    /**
     * CachedCompositeImageFilter constructor.
     *
     * @param Cache $cache
     * @param array $request
     */
    public function __construct(Cache $cache, array $request) {
        $this->cache = $cache;
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
     */
    public function apply(Image $image) {
        sort($this->filtersNames);
        $toHash = join('', $this->filtersNames) . $this->request['src'];
        if (isset ($this->request['width'])) {
            $toHash .= $this->request['width'];
        }
        if (isset ($this->request['height'])) {
            $toHash .= $this->request['height'];
        }
        $hash = md5($toHash);
        $filtered = $this->cache->get($hash, function () use ($image) {
            return parent::apply($image);
        });
        return $filtered;
    }

}
