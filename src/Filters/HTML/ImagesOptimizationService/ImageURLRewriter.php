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
     * ImagesOptimizationServiceHTMLFilter constructor.
     *
     * @param ServiceSignature $signature
     * @param Retriever $retriever
     * @param URL $baseUrl
     * @param URL $serviceUrl
     * @param string[] $whitelist
     */
    public function __construct(
        ServiceSignature $signature,
        Retriever $retriever,
        URL $baseUrl,
        URL $serviceUrl,
        array $whitelist
    ) {
        $this->signature = $signature;
        $this->retriever = $retriever;
        $this->baseUrl = $baseUrl;
        $this->serviceUrl = $serviceUrl;
        $this->whitelist = $whitelist;
    }

    /**
     * @param string $url
     * @param URL|null $baseUrl
     * @param array $params
     * @return string
     */
    public function rewriteUrl($url, URL $baseUrl = null, array $params = []) {
        $absolute = $this->makeURLAbsoluteToBase($url, $baseUrl);
        if ($this->shouldRewriteUrl($absolute)) {
            $params['src'] = $absolute;
            return $this->makeSignedUrl($params);
        }
        return $url;
    }

    /**
     * @param $styleContent
     * @return string
     */
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
                return $matches[1] . $this->rewriteUrl($matches[2]);
            },
            $styleContent
        );
    }

    /**
     * @param string $url
     * @param URL|null $baseUrl
     * @return null|string
     */
    private function makeURLAbsoluteToBase($url, URL $baseUrl = null) {
        $url = trim($url);
        if (!$url || substr($url, 0, 5) === 'data:') {
            return null;
        }
        $this->logger()->info('Rewriting img {url}', ['url' => $url]);
        $baseUrl = is_null($baseUrl) ? $this->baseUrl : $baseUrl;
        return URL::fromString($url)->withBase($baseUrl)->toString();
    }

    /**
     * @param string $url
     * @return bool
     */
    private function shouldRewriteUrl($url) {
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

    /**
     * @param array $params
     * @return string
     */
    private function makeSignedUrl(array $params) {
        $modTime = $this->retriever->getLastModificationTime(URL::fromString($params['src']));
        if ($modTime) {
            $params['cacheMarker'] = $modTime;
        }
        return (new ServiceRequest())->withParams($params)
            ->withUrl($this->serviceUrl)
            ->sign($this->signature)
            ->serialize();
    }
}
