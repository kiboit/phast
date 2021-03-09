<?php
namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Filters\HTML\AMPCompatibleFilter;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements HTMLStreamFilter, AMPCompatibleFilter {
    const IMG_SRC_ATTR_PATTERN = '~^(|data-(|lazy-|wood-))src$~i';

    const IMG_SRCSET_ATTR_PATTERN = '~^(|data-(|lazy-|wood-))srcset$~i';

    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    private $inPictureTag = false;

    private $inBody = false;

    private $imagePathPattern;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
        $this->imagePathPattern = $this->makeImagePathPattern();
    }

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        foreach ($elements as $element) {
            if ($element instanceof Tag) {
                $this->handleTag($element, $context);
            } elseif ($element instanceof ClosingTag) {
                $this->handleClosingTag($element);
            }
            yield $element;
        }
    }

    private function handleTag(Tag $tag, HTMLPageContext $context) {
        $isImage = false;
        if ($tag->getTagName() == 'img'
            || ($this->inPictureTag && $tag->getTagName() == 'source')
            || $tag->getTagName() == 'amp-img'
        ) {
            $isImage = true;
        } elseif ($tag->getTagName() == 'picture') {
            $this->inPictureTag = true;
        } elseif ($tag->getTagName() == 'video' || $tag->getTagName() == 'audio') {
            $this->inPictureTag = false;
        } elseif ($tag->getTagName() == 'body') {
            $this->inBody = true;
        } elseif ($tag->getTagName() == 'meta') {
            return;
        }

        foreach ($tag->getAttributes() as $k => $v) {
            if (!$v) {
                continue;
            }
            if ($isImage && preg_match(self::IMG_SRC_ATTR_PATTERN, $k)) {
                $this->rewriteSrc($tag, $context, $k);
            } elseif ($isImage && preg_match(self::IMG_SRCSET_ATTR_PATTERN, $k)) {
                $this->rewriteSrcset($tag, $context, $k);
            } elseif ($this->inBody && preg_match($this->imagePathPattern, parse_url($v, PHP_URL_PATH))) {
                $this->rewriteArbitraryAttribute($tag, $context, $k);
            }
        }
    }

    private function makeImagePathPattern() {
        $pieces = [];
        foreach (Resource::EXTENSION_TO_MIME_TYPE as $ext => $mime) {
            if (strpos($mime, 'image/') === 0) {
                $pieces[] = preg_quote($ext, '~');
            }
        }
        return '~\.(?:' . implode('|', $pieces) . ')$~';
    }

    private function handleClosingTag(ClosingTag $closingTag) {
        if ($closingTag->getTagName() == 'picture') {
            $this->inPictureTag = false;
        }
    }

    private function rewriteSrc(Tag $img, HTMLPageContext $context, $attribute) {
        $url = $img->getAttribute($attribute);
        if (preg_match('~(images|assets)/transparent\.png~', $url) && $img->hasClass('rev-slidebg')) {
            return $url;
        }
        $newURL = $this->rewriter->rewriteUrl($url, $context->getBaseUrl());
        $img->setAttribute($attribute, $newURL);
    }

    private function rewriteSrcset(Tag $img, HTMLPageContext $context, $attribute) {
        $srcset = $img->getAttribute($attribute);
        $rewritten = preg_replace_callback('/([^,\s]+)(\s+(?:[^,]+))?/', function ($match) use ($context) {
            $url = $this->rewriter->rewriteUrl($match[1], $context->getBaseUrl());
            if (isset($match[2])) {
                return $url . $match[2];
            }
            return $url;
        }, $srcset);
        $img->setAttribute($attribute, $rewritten);
    }

    private function rewriteArbitraryAttribute(Tag $element, HTMLPageContext $context, $attribute) {
        $url = $element->getAttribute($attribute);
        $newUrl = $this->rewriter->rewriteUrl($url, $context->getBaseUrl(), [], true);
        $element->setAttribute($attribute, $newUrl);
    }
}
