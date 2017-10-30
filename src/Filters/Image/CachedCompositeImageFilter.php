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
        $filters = join('', $this->filtersNames);
        $hash = md5($filters . $this->request['src'] . $this->request['width'] . $this->request['height']);
        $filtered = null;
        $data = $this->cache->get($hash, function () use ($image, &$filtered) {
            $filtered = parent::apply($image);
            return serialize($filtered);
        });
        return is_null($filtered) ? unserialize($data) : $filtered;
    }

}
