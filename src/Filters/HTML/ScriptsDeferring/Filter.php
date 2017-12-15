<?php

namespace Kibo\Phast\Filters\HTML\ScriptsDeferring;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Filters\HTML\Helpers\JSDetectorTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;

class Filter implements HTMLFilter {
    use JSDetectorTrait, BodyFinderTrait;

    private $rewriteScript = <<<EOS
(function () {
    var deferreds = [];
    var replace = function (original, rewritten) {
        original.parentNode.insertBefore(rewritten, original);
        original.parentNode.removeChild(original);
    };
    var lastScript;
    Object.defineProperty(document, 'readyState', {
        configurable: true,
        get: function() {
            return 'loading';
        }
    });
    Array.prototype.forEach.call(document.querySelectorAll('script[type="phast-script"]'), function (el) {
        var script = document.createElement('script');
        Array.prototype.forEach.call(el.attributes, function (attr) {
            script.setAttribute(attr.nodeName, attr.nodeValue);
        });
        if (!el.hasAttribute('async')) {
            script.async = false;
        }
        if (el.hasAttribute('data-phast-original-type')) {
            script.setAttribute('type', el.getAttribute('data-phast-original-type'));
            script.removeAttribute('data-phast-original-type');
        } else {
            script.removeAttribute('type');
        }
        if (!el.hasAttribute('src')) {
            script.setAttribute('src', 'data:text/javascript;base64,' + utoa(el.textContent));
        }
        if (el.hasAttribute('defer')) {
            deferreds.push({original: el, rewritten: script});
        } else {
            replace(el, script);
            lastScript = script;
        }
    });
    deferreds.forEach(function (deferred) {
        replace(deferred.original, deferred.rewritten);
    });
    lastScript.onload  = restoreReadyState;
    lastScript.onerror = restoreReadyState;
    function restoreReadyState() {
        delete document['readyState'];

        if (document.onreadystatechange) {
            exec(document.onreadystatechange, document);
        }

        var dcl = document.createEvent("Event");
        dcl.initEvent("DOMContentLoaded", true, true);
        window.document.dispatchEvent(dcl);
    }
    function exec(func, opt_scopeObject) {
        try {
            func.call(opt_scopeObject || window);
        } catch (err) {}
    }
    function utoa(str) {
        return btoa(
            encodeURIComponent(str).replace(
                /%([0-9A-F]{2})/g,
                function toSolidBytes(match, p1) {
                    return String.fromCharCode('0x' + p1);
                }
            )
        );
    }
})();
EOS;


    public function transformHTMLDOM(\Kibo\Phast\Common\DOMDocument $document) {
        $body = $this->getBodyElement($document);
        foreach ($document->query('//script') as $script) {
            if ($script->hasAttribute('data-phast-no-defer')) {
                $script->removeAttribute('data-phast-no-defer');
            } elseif ($this->isJSElement($script)) {
                $this->rewrite($script);
            }
        }
        $rewriteScript = $document->createElement('script');
        $rewriteScript->textContent = $this->rewriteScript;
        $body->appendChild($rewriteScript);
    }

    private function rewrite(\DOMElement $script) {
        if ($script->hasAttribute('type')) {
            $script->setAttribute('data-phast-original-type', $script->getAttribute('type'));
        }
        $script->setAttribute('type', 'phast-script');
    }

}
