<?php


namespace Kibo\Phast\Filters\HTML;


use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;
use Kibo\Phast\Services\ServiceRequest;
use Kibo\Phast\ValueObjects\URL;

class DiagnosticsHTMLFilter implements HTMLFilter {
    use BodyFinderTrait;

    private $script = <<<EOS
(function () {
    window.addEventListener('load', function () {
        var url = '%s';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.responseType = 'json';
        xhr.onload = function () {
            var entries = xhr.response;
            var startTime = entries[0].context.timestamp;
            console.group('Phast diagnostics log');
            entries.forEach(function (entry) {
                var prefixKeys = ['timestamp', 'service', 'class', 'method', 'line'];
                var prefix = '';
                prefixKeys.forEach(function (key) {
                    if (entry.context[key]) {
                        prefix += '{' + key + '}\t';
                    }
                });
                var log = (prefix + entry.message).replace(/\{([a-z0-9_.]*)\}/gi, function (match, group) {
                    if (group === 'timestamp') {
                        return entry.context.timestamp - startTime;
                    }
                    return entry.context[group];    
                });
                
                if (entry.level > 8) {
                    console.error(log);
                } else if (entry.level === 8) {
                    console.warn(log);
                } else if (entry.level > 1) {
                    console.info(log);
                } else {
                    console.log(log);
                }
            });
            console.groupEnd();
        };
        xhr.send();        
    });
})();
EOS;

    private $serviceUrl;

    public function __construct($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $script = $document->createElement('script');
        $url = (new ServiceRequest())->withUrl(URL::fromString($this->serviceUrl))->serialize();
        $script->textContent = sprintf($this->script, $url);
        $body = $this->getBodyElement($document);
        $body->appendChild($script);
    }


}
