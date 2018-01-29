<?php

namespace Kibo\Phast\Filters\Service;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class CachedFilter  implements ServiceFilter {
    use LoggingTrait;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var CachedResultServiceFilter
     */
    private $cachedFilter;

    /**
     * CachedFilter constructor.
     * @param Cache $cache
     * @param CachedResultServiceFilter $cachedFilter
     */
    public function __construct(Cache $cache, CachedResultServiceFilter $cachedFilter) {
        $this->cache = $cache;
        $this->cachedFilter = $cachedFilter;
    }


    /**
     * @param Resource $resource
     * @param array $request
     * @return Resource
     * @throws CachedExceptionException
     */
    public function apply(Resource $resource, array $request) {
        $key = $this->cachedFilter->getCacheHash($resource, $request);
        $this->logger()->info('Trying to get {url} from cache', ['url' => (string)$resource->getUrl()]);
        $result = $this->cache->get($key, function () use ($resource, $request) {
            $this->logger()->info('Cache missed!');
            try {
                $resource = $this->cachedFilter->apply($resource, $request);
                return $this->serializeResource($resource);
            } catch (\Exception $e) {
                return $this->serializeException($e);
            }
        }, $resource->getLastModificationTime() ? 0 : 86400);
        if ($result['dataType'] == 'exception') {
            throw $this->deserializeException($result);
        }
        return $this->deserializeResource($result);
    }

    private function serializeResource(Resource $resource) {
        return [
            'dataType' => 'resource',
            'url' => $resource->getUrl()->toString(),
            'mimeType' => $resource->getMimeType(),
            'blob' => base64_encode($resource->getContent())
        ];
    }

    private function deserializeResource(array $data) {
        return Resource::makeWithContent(
            URL::fromString($data['url']),
            $data['mimeType'],
            base64_decode($data['blob'])
        );
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
