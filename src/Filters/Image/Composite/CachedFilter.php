<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Filters\Image\Image;
use Kibo\Phast\Filters\Image\ImageImplementations\DummyImage;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class CachedFilter  {
    use LoggingTrait;

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
    private $filtersNames = [];

    /**
     * CachedCompositeImageFilter constructor.
     *
     * @param Cache $cache
     * @param Retriever $retriever
     */
    public function __construct(Cache $cache, Retriever $retriever) {
        $this->cache = $cache;
        $this->retriever = $retriever;
    }

    /**
     * @param Image $image
     * @param array $request
     * @return Image
     * @throws CachedExceptionException
     */
    public function apply(Image $image, array $request) {
        $resource = Resource::makeWithRetriever(URL::fromString($request['src']), 'image/jpeg', $this->retriever);
        $key = ''; //parent::getCacheHash($resource, $request);
        $this->logger()->info('Trying to get {url} from cache', ['url' => $request['src']]);
        $result = $this->cache->get($key, function () use ($image, $request) {
            $this->logger()->info('Cache missed!');
            try {
                return 'test';
                //return $this->serializeImage(parent::apply($image, $request));
            } catch (\Exception $e) {
                return $this->serializeException($e);
            }
        }, $resource->getLastModificationTime() ? 0 : 86400);
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
