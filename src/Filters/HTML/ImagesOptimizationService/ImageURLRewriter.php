<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class ImageURLRewriter {
    use LoggingTrait;

    /**
     * @var ServiceSignature
     */
    protected $signature;

    /**
     * @var Retriever
     */
    protected $retriever;

    /**
     * @var URL
     */
    protected $baseUrl;

    /**
     * @var URL
     */
    protected $serviceUrl;

    /**
     * @var string[]
     */
    protected $whitelist;

    /**
     * @var integer
     */
    protected $serviceRequestFormat;

    /**
     * ImagesOptimizationServiceHTMLFilter constructor.
     *
     * @param ServiceSignature $signature
     * @param Retriever $retriever
     * @param URL $baseUrl
     * @param URL $serviceUrl
     * @param string[] $whitelist
     * @param null|int $serviceRequestFormat
     */
    public function __construct(
        ServiceSignature $signature,
        Retriever $retriever,
        URL $baseUrl,
        URL $serviceUrl,
        array $whitelist,
        $serviceRequestFormat = null
    ) {
        $this->signature = $signature;
        $this->retriever = $retriever;
        $this->baseUrl = $baseUrl;
        $this->serviceUrl = $serviceUrl;
        $this->whitelist = $whitelist;
        $this->serviceRequestFormat = $serviceRequestFormat == ServiceRequest::FORMAT_PATH
            ? ServiceRequest::FORMAT_PATH
            : ServiceRequest::FORMAT_QUERY;
    }

    public function makeURLAbsoluteToBase($url) {
        if (!$url || substr($url, 0, 5) === 'data:') {
            return null;
        }
        $this->logger()->info('Rewriting img {url}', ['url' => $url]);
        return URL::fromString($url)->withBase($this->baseUrl)->toString();
    }

    public function shouldRewriteUrl($url) {
        if (!$url) {
            return false;
        }
        foreach ($this->whitelist as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        return false;
    }

    public function makeSignedUrl($params) {
        $modTime = $this->retriever->getLastModificationTime(URL::fromString($params['src']));
        if ($modTime) {
            $params['cacheMarker'] = $modTime;
        }
        return (new ServiceRequest())->withParams($params)
            ->withUrl($this->serviceUrl)
            ->sign($this->signature)
            ->serialize($this->serviceRequestFormat);
    }

    // TODO: extract this method in TextResourceFilter
    // TODO: use the derived class into TextResource filtering
    public function rewriteStyle($styleContent) {
        return preg_replace_callback(
            '~
                (
                    \b (?: image | background ):
                    [^;}]*
                    \b url \( [\'"]?
                )
                (
                    [^\'")] ++
                )
            ~xiS',
            function ($matches) {
                $url = $this->makeURLAbsoluteToBase($matches[2]);
                if ($this->shouldRewriteUrl($url)) {
                    $params = ['src' => $url];
                    return $matches[1] . $this->makeSignedUrl($params);
                }
                return $matches[1] . $matches[2];
            },
            $styleContent
        );
    }
}
