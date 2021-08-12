<?php

namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

use Kibo\Phast\Filters\HTML\BaseHTMLStreamFilter;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter extends BaseHTMLStreamFilter {
    use JSDetectorTrait;

    /** @var array */
    private $csp;

    /**
     * @param array $csp
     */
    public function __construct(array $csp) {
        $this->csp = $csp;
    }

    protected function isTagOfInterest(Tag $tag) {
        return $tag->getTagName() == 'script';
    }

    protected function handleTag(Tag $script) {
        if ($this->isJSElement($script)
            && !$this->isDeferralDisabled($script)
        ) {
            $this->rewrite($script);
        }
        yield $script;
    }

    protected function afterLoop() {
        $scriptsLoader = PhastJavaScript::fromFile(__DIR__ . '/scripts-loader.js');
        $scriptsLoader->setConfig('scriptsLoader', [
            'csp' => $this->csp,
        ]);
        $this->context->addPhastJavaScript($scriptsLoader);
        $this->context->addPhastJavaScript(PhastJavaScript::fromFile(__DIR__ . '/rewrite.js'));
    }

    private function rewrite(Tag $script) {
        if (!$script->hasAttribute('src')) {
            $script->removeAttribute('async');
            $script->removeAttribute('defer');
        }
        if ($script->hasAttribute('type')) {
            $script->setAttribute('data-phast-original-type', $script->getAttribute('type'));
        }
        $script->setAttribute('type', 'text/phast');
        if ($script->hasAttribute('data-phast-params')) {
            $script->removeAttribute('src');
        }
        if (!$script->hasAttribute('src')) {
            $this->convertBooleanAttribute($script, 'async');
            $this->convertBooleanAttribute($script, 'defer');
        }
    }

    private function isDeferralDisabled(Tag $script) {
        return $script->hasAttribute('data-phast-no-defer')
               || $script->hasAttribute('data-pagespeed-no-defer')
               || $script->getAttribute('data-cfasync') === 'false'
               || preg_match('~^\s*(?<q>[\'"])phast-no-defer\k<q>~', $script->textContent);
    }

    private function convertBooleanAttribute(Tag $script, $attr) {
        if ($script->hasAttribute($attr)) {
            $script->removeAttribute($attr);
            $script->setAttribute('data-phast-' . $attr, '');
        }
    }
}
