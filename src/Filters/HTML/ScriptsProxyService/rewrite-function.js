(function(config) {
    var urlPattern = /^(https?:)?\/\//;
    var cacheMarker = Math.floor((new Date).getTime() / 1000 / config.urlRefreshTime);
    var id = 0;
    var whitelist = compileWhitelistPatterns(config.whitelist);

    overrideDOMMethod('appendChild');
    overrideDOMMethod('insertBefore');

    function compileWhitelistPatterns(patterns) {
        var re = /^(.)(.*)\1([a-z]*)$/i;
        var compiled = [];
        patterns.forEach(function (pattern) {
            var match = re.exec(pattern);
            if (!match) {
                window.console && window.console.log("Phast: Not a pattern:", pattern);
                return;
            }
            try {
                compiled.push(new RegExp(match[2], match[3]));
            } catch (e) {
                window.console && window.console.log("Phast: Failed to compile pattern:", pattern);
            }
        });
        return compiled;
    }

    function checkWhitelist(value) {
        for (var i = 0; i < whitelist.length; i++) {
            if (whitelist[i].exec(value)) {
                return true;
            }
        }
        return false;
    }

    function overrideDOMMethod(name) {
        var original = Element.prototype[name];
        Element.prototype[name] = function () {
            processNode(arguments[0]);
            return original.apply(this, arguments);
        };
    }

    function processNode(el) {
        if (!el) {
            return;
        }
        if (el.nodeType !== Node.ELEMENT_NODE) {
            return;
        }
        if (el.tagName !== 'SCRIPT') {
            return;
        }
        if (!urlPattern.test(el.src)) {
            return;
        }
        if (el.src.substr(0, config.serviceUrl.length) == config.serviceUrl) {
            return;
        }
        if (!checkWhitelist(el.src)) {
            return;
        }
        id++;
        el.setAttribute('data-phast-proxied-script', id);
        el.src = config.serviceUrl + '&src=' + escape(el.src) +
            '&cacheMarker=' + escape(cacheMarker) +
            '&id=d' + id;
    }
});
