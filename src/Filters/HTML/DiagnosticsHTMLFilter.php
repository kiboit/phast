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
            var logGroupsMap = {};
            var logGroupsArr = [];
            entries.forEach(function (entry) {
                var requestId = entry.context.requestId;
                if (!logGroupsMap[requestId]) {
                    logGroupsMap[requestId] = {
                        title: entry.context.service,
                        timestamp: entry.context.timestamp,
                        errorsCnt: 0,
                        warningsCnt: 0,
                        longestPrefixLength: 0,
                        entries: []
                    };
                    logGroupsArr.push(logGroupsMap[requestId]);
                }
                
                if (entry.level > 8) {
                    logGroupsMap[requestId].errorsCnt++;
                } else if (entry.level === 8) {
                    logGroupsMap[requestId].warningsCnt++;
                }
                
                var prefix = (entry.context.timestamp - logGroupsMap[requestId].timestamp).toFixed(3);
                if (entry.context.class) {
                    prefix += ' ' + entry.context.class;
                }
                if (entry.context.class && entry.context.method) {
                    prefix += '::';
                }
                if (entry.context.method) {
                    prefix += entry.context.method + '()';
                }
                if (entry.context.line) {
                    prefix += ' Line: ' + entry.context.line;
                }
                var message = entry.message.replace(/\{([a-z0-9_.]*)\}/gi, function (match, matchedGroup) {
                    return entry.context[matchedGroup];    
                });
                
                var callback;
                if (entry.level > 8) {
                    callback = console.error;
                } else if (entry.level === 8) {
                    callback = console.warn;
                } else if (entry.level > 1) {
                    callback = console.info;
                } else {
                    callback = console.log;
                }
                logGroupsMap[requestId].entries.push({
                    prefix: prefix,
                    message: message,
                    cb: callback                
                });
                if (prefix.length > logGroupsMap[requestId].longestPrefixLength) {
                    logGroupsMap[requestId].longestPrefixLength = prefix.length;
                }
            });
            
            if (logGroupsArr.length === 0) {
                return;
            }
            
            logGroupsArr.sort(function (g1, g2) {
                return g1.timestamp < g2.timestamp ? -1 : 1;
            });
            
            var startTime = logGroupsArr[0].timestamp;
            console.group('Phast diagnostics log');
            logGroupsArr.forEach(function (group) {
                var groupStartOffset = (group.timestamp - startTime).toFixed(3);
                var title = groupStartOffset + ' - ' + group.title + ' (entries: ' + group.entries.length;
                if (group.errorsCnt > 0) {
                    title += ', errors: ' + group.errorsCnt;
                }
                if (group.warningsCnt > 0) {
                    title += ', warnings: ' + group.warningsCnt; 
                }
                title += ')';
                console.groupCollapsed(title);
                group.entries.forEach(function (entry) {
                    var prefix = entry.prefix;
                    var padCount = group.longestPrefixLength - prefix.length;
                    for (var i = 0; i < padCount; i++) {
                        prefix += ' ';
                    }
                    entry.cb(prefix + ' ' + entry.message);
                });
                console.groupEnd();
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
