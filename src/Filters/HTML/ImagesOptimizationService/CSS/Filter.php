<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;

class Filter implements HTMLFilter {

    /**
     * @var ImageURLRewriter
     */
    protected $rewriter;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $styleTags = $document->query('//style');
        /** @var \DOMElement $styleTag */
        foreach ($styleTags as $styleTag) {
            $styleTag->textContent = $this->rewriteStyle($styleTag->textContent);
        }

        $styleAttrs = $document->query('//@style');
        /** @var \DOMAttr $styleAttr */
        foreach ($styleAttrs as $styleAttr) {
            $styleAttr->value = htmlspecialchars($this->rewriteStyle($styleAttr->value));
        }
    }

    private function rewriteStyle($styleContent) {
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
                $url = $this->rewriter->makeURLAbsoluteToBase($matches[2]);
                if ($this->rewriter->shouldRewriteUrl($url)) {
                    $params = ['src' => $url];
                    return $matches[1] . $this->rewriter->makeSignedUrl($params);
                }
                return $matches[1] . $matches[2];
            },
            $styleContent
        );
    }

}
