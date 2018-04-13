<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\Resource;
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
     * @var int
     */
    protected $maxImageInliningSize;

    /**
     * @var Resource[]
     */
    protected $inlinedResources;


    /**
     * ImageURLRewriter constructor.
     * @param ServiceSignature $signature
     * @param LocalRetriever $retriever
     * @param URL $baseUrl
     * @param URL $serviceUrl
     * @param array $whitelist
     * @param int $maxImageInliningSize
     */
    public function __construct(
        ServiceSignature $signature,
        LocalRetriever $retriever,
        URL $baseUrl,
        URL $serviceUrl,
        array $whitelist,
        $maxImageInliningSize = 0
    ) {
        $this->signature = $signature;
        $this->retriever = $retriever;
        $this->baseUrl = $baseUrl;
        $this->serviceUrl = $serviceUrl;
        $this->whitelist = $whitelist;
        $this->maxImageInliningSize = $maxImageInliningSize;
    }

    /**
     * @param string $url
     * @param URL|null $baseUrl
     * @param array $params
     * @return string
     */
    public function rewriteUrl($url, URL $baseUrl = null, array $params = []) {
        $this->inlinedResources = [];
        $absolute = $this->makeURLAbsoluteToBase($url, $baseUrl);
        if (!$this->shouldRewriteUrl($absolute)) {
            return $url;
        }
        $size = $this->retriever->getSize($absolute);
        if ($size !== false && $size < $this->maxImageInliningSize) {
            $dataUrl = $this->makeDataUrl($absolute);
            if ($dataUrl) {
                return $dataUrl;
            }
        }
        $params['src'] = $absolute->toString();
        return $this->makeSignedUrl($params);
    }

    /**
     * @param $styleContent
     * @return string
     */
    public function rewriteStyle($styleContent) {
        $allInlined = [];
        $result = preg_replace_callback(
            '~
                (\b (?: image | background ):)
                ([^;}]*)
            ~xiS',
            function ($match) use (&$allInlined) {
                return $match[1] . $this->rewriteStyleRule($match[2], $allInlined);
            },
            $styleContent
        );
        $this->inlinedResources = array_values($allInlined);
        return $result;
    }

    private function rewriteStyleRule($ruleContent, &$allInlined) {
        return preg_replace_callback(
            '~
                ( \b url \( [\'"]? )
                ( [^\'")] ++ )
            ~xiS',
            function ($match) use (&$allInlined) {
                $url = $match[1] . $this->rewriteUrl($match[2]);
                if (!empty ($this->inlinedResources)) {
                    $inlined = $this->inlinedResources[0];
                    $allInlined[$inlined->getUrl()->toString()] = $inlined;
                }
                return $url;
            },
            $ruleContent
        );
    }

    /**
     * @return Resource[]
     */
    public function getInlinedResources() {
        return $this->inlinedResources;
    }

    /**
     * @return string
     */
    public function getCacheSalt() {
        $parts = array_merge([
            $this->signature->getCacheSalt(),
            $this->baseUrl->toString(),
            $this->serviceUrl->toString(),
            $this->maxImageInliningSize
        ], array_keys($this->whitelist), array_values($this->whitelist));
        return join('-', $parts);
    }

    /**
     * @param string $url
     * @param URL|null $baseUrl
     * @return URL
     */
    private function makeURLAbsoluteToBase($url, URL $baseUrl = null) {
        $url = trim($url);
        if (!$url || substr($url, 0, 5) === 'data:') {
            return null;
        }
        $this->logger()->info('Rewriting img {url}', ['url' => $url]);
        $baseUrl = is_null($baseUrl) ? $this->baseUrl : $baseUrl;
        return URL::fromString($url)->withBase($baseUrl);
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

    private function makeDataUrl(URL $url) {
        $content = $this->retriever->retrieve($url);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);
        if ($mime && substr($mime, 0, 6) == 'image/') {
            $this->inlinedResources = [Resource::makeWithRetriever($url, $this->retriever, $mime)];
            return 'data:' . $mime . ';base64,' . base64_encode($content);
        }
        return false;
    }

    /**
     * @param array $params
     * @return string
     */
    private function makeSignedUrl(array $params) {
        $params['cacheMarker'] = $this->retriever->getCacheSalt(URL::fromString($params['src']));
        return (new ServiceRequest())->withParams($params)
            ->withUrl($this->serviceUrl)
            ->sign($this->signature)
            ->serialize();
    }
}
