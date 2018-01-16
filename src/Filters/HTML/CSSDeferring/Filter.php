<?php

namespace Kibo\Phast\Filters\HTML\CSSDeferring;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Filters\HTML\HTMLFilter;
use Kibo\Phast\Logging\LoggingTrait;

class Filter implements HTMLFilter {
    use BodyFinderTrait, LoggingTrait;

    const LOADER_JS = <<<EOS
(function() {
    var scripts = document.querySelectorAll('script[type="phast-link"]');

    Array.prototype.forEach.call(scripts, function(script) {
        var replacement = document.createElement('div');
        replacement.innerHTML = script.textContent;
        while (replacement.firstChild) {
            script.parentNode.insertBefore(replacement.firstChild, script);
        }
        script.parentNode.removeChild(script);
    });
})();
EOS;

    public function transformHTMLDOM(\Kibo\Phast\Common\DOMDocument $document) {
        $insert_loader = false;

        foreach (iterator_to_array($document->query('//link')) as $link) {
            if ($link->getAttribute('rel') != 'stylesheet') {
                continue;
            }

            $this->logger()->info('Differing {src}', ['src' => $link->getAttribute('href')]);
            $script = $document->createElement('script', trim($document->saveHTML($link)));
            $script->setAttribute('type', 'phast-link');

            $link->parentNode->insertBefore($script, $link);
            $link->parentNode->removeChild($link);

            $insert_loader = true;
        }

        if ($insert_loader) {
            $this->logger()->info('Inserting JS loader');
            $loader = $document->createElement('script', self::LOADER_JS);
            $loader->setAttribute('data-phast-no-defer', '');
            $this->getBodyElement($document)->appendChild($loader);
        } else {
            $this->logger()->info('No links were deferred. Not inserting JS loader.');
        }
    }

}
