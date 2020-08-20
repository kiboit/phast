<?php
namespace Kibo\Phast\Services;

use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Exceptions\UnauthorizedException;
use Kibo\Phast\HTTP\Response;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

abstract class BaseService {
    use LoggingTrait;

    /**
     * @var ServiceSignature
     */
    protected $signature;

    /**
     * @var string[]
     */
    protected $whitelist = [];

    /**
     * @var Retriever
     */
    protected $retriever;

    /**
     * @var ServiceFilter
     */
    protected $filter;

    /**
     * @var array
     */
    protected $config;

    /**
     * BaseService constructor.
     * @param ServiceSignature $signature
     * @param array $whitelist
     * @param Retriever $retriever
     * @param ServiceFilter $filter
     * @param array $config
     */
    public function __construct(
        ServiceSignature $signature,
        array $whitelist,
        Retriever $retriever,
        ServiceFilter $filter,
        array $config
    ) {
        $this->signature = $signature;
        $this->whitelist = $whitelist;
        $this->retriever = $retriever;
        $this->filter = $filter;
        $this->config = $config;
    }

    /**
     * @param ServiceRequest $request
     * @return Response
     */
    public function serve(ServiceRequest $request) {
        $this->validateRequest($request);
        $request = $this->getParams($request);
        $resource = Resource::makeWithRetriever(
            URL::fromString(isset($request['src']) ? $request['src'] : ''),
            $this->retriever
        );
        $filtered = $this->filter->apply($resource, $request);
        return $this->makeResponse($filtered, $request);
    }

    /**
     * @param ServiceRequest $request
     * @return array
     */
    protected function getParams(ServiceRequest $request) {
        return $request->getParams();
    }

    /**
     * @param Resource $resource
     * @param array $request
     * @return Response
     */
    protected function makeResponse(Resource $resource, array $request) {
        $response = new Response();
        $response->setContent($resource->getContent());
        return $response;
    }

    protected function validateRequest(ServiceRequest $request) {
        $this->validateIntegrity($request);
        try {
            $this->validateToken($request);
        } catch (UnauthorizedException $e) {
            $this->validateWhitelisted($request);
        }
    }

    protected function validateIntegrity(ServiceRequest $request) {
        $params = $request->getParams();
        if (!isset($params['src'])) {
            throw new ItemNotFoundException('No source is set!');
        }
    }

    protected function validateToken(ServiceRequest $request) {
        if (!$request->verify($this->signature)) {
            throw new UnauthorizedException('Invalid token in request: ' .
                $request->serialize(ServiceRequest::FORMAT_QUERY));
        }
    }

    protected function validateWhitelisted(ServiceRequest $request) {
        $params = $request->getParams();
        foreach ($this->whitelist as $pattern) {
            if (preg_match($pattern, $params['src'])) {
                return;
            }
        }
        throw new UnauthorizedException('Not allowed url: ' . $params['src']);
    }
}
