<?php

namespace Kibo\Phast\Filters\Service;

use Kibo\Phast\Cache\Cache;
use Kibo\Phast\Exceptions\CachedExceptionException;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class CachingServiceFilter implements ServiceFilter {
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
     * @var Retriever
     */
    private $retriever;

    /**
     * CachingServiceFilter constructor.
     * @param Cache $cache
     * @param CachedResultServiceFilter $cachedFilter
     * @param Retriever $retriever
     */
    public function __construct(Cache $cache, CachedResultServiceFilter $cachedFilter, Retriever $retriever) {
        $this->cache = $cache;
        $this->cachedFilter = $cachedFilter;
        $this->retriever = $retriever;
    }

    /**
     * @param Resource $resource
     * @param array $request
     * @return Resource
     * @throws CachedExceptionException
     */
    public function apply(Resource $resource, array $request) {
        $key = $this->cachedFilter->getCacheSalt($resource, $request);
        $this->logger()->info('Trying to get {url} from cache', ['url' => (string) $resource->getUrl()]);
        $result = $this->cache->get($key);
        if (isset($result['encoding']) && $result['encoding'] != 'identity') {
            $result = null;
        }
        if ($result && $this->checkDependencies($result)) {
            return $this->deserializeCachedData($result);
        }
        try {
            $result = $this->cachedFilter->apply($resource, $request);
            $this->cache->set($key, $this->serializeResource($result));
            return $result;
        } catch (\Exception $e) {
            $cachingException = $this->serializeException($e);
            $this->cache->set($key, $cachingException);
            throw $this->deserializeException($cachingException);
        }
    }

    private function checkDependencies(array $data) {
        foreach ((array) @$data['dependencies'] as $dep) {
            $url = URL::fromString($dep['url']);
            if ($this->retriever->getCacheSalt($url) >= $dep['cacheMarker']) {
                return false;
            }
        }
        return true;
    }

    private function deserializeCachedData(array $data) {
        if ($data['dataType'] == 'exception') {
            throw $this->deserializeException($data);
        }
        return $this->deserializeResource($data);
    }

    private function serializeResource(Resource $resource) {
        return [
            'dataType' => 'resource',
            'url' => $resource->getUrl()->toString(),
            'mimeType' => $resource->getMimeType(),
            'blob' => $resource->getContent(),
            'dependencies' => $this->serializeDependencies($resource),
        ];
    }

    private function serializeDependencies(Resource $resource) {
        return array_map(function (Resource $dep) {
            return [
                'url' => $dep->getUrl()->toString(),
                'cacheMarker' => $dep->getCacheSalt(),
            ];
        }, $resource->getDependencies());
    }

    private function deserializeResource(array $data) {
        $params = [
            URL::fromString($data['url']),
            $data['blob'],
            $data['mimeType'],
        ];
        return Resource::makeWithContent(...$params);
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
                'Phast: %s: Type: %s, Msg: %s, Code: %s',
                static::class,
                $data['class'],
                $data['msg'],
                $data['code']
            )
        );
    }
}
