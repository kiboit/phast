<?php

namespace Kibo\Phast\Filters\Image;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
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
        $result = $this->cache->get($key, function () use ($image) {
            try {
                return $this->serializeImage(parent::apply($image));
            } catch (\Exception $e) {
                return $this->serializeException($e);
            }
        }, $lastModTime ? 0 : 86400);
        if ($result['dataType'] == 'exception') {
            throw $this->deserializeException($result);
        }
        return $this->deserializeImage($result);
    }

    private function serializeImage(Image $image) {
        return [
            'dataType' => 'image',
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'type' => $image->getType(),
            'blob' => base64_encode($image->getAsString())
        ];
    }

    private function deserializeImage(array $data) {
        $image = new DummyImage($data['width'], $data['height']);
        $image->setType($data['type']);
        $image->setImageString(base64_decode($data['blob']));
        return $image;
    }

    private function serializeException(\Exception $e) {
        return [
            'dataType' => 'exception',
            'class' => get_class($e),
            'msg' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
    }

    private function deserializeException(array $data) {
        return new CachedExceptionException(
            sprintf(
                'Phast: CachedCompositeImageFilter: Type: %s, Msg: %s, Code: %s',
                $data['class'],
                $data['msg'],
                $data['code']
            )
        );
    }

}
