var Promise = phast.ES6Promise;

phast.ScriptsLoader = {};

phast.ScriptsLoader.getScriptsInExecutionOrder = function (document, factory) {
    var elements = document.querySelectorAll('script[type="phast-script"]');
    var nonDeferred = [], deferred = [];
    for (var i = 0; i < elements.length; i++) {
        if (getSrc(elements[i]) !== undefined && elements[i].hasAttribute('defer')) {
            deferred.push(elements[i]);
        } else {
            nonDeferred.push(elements[i]);
        }
    }
    return nonDeferred.concat(deferred).map(function (element) {
        return factory.makeScriptFromElement(element);
    });
};

phast.ScriptsLoader.executeScripts = function (scripts) {

    var initializers = scripts.map(function (script) {
        return script.init();
    });

    var lastScript = Promise.resolve();

    scripts.forEach(function (script) {
        var description;
        try {
            if (script.describe) {
                description = script.describe();
            } else {
                description = 'unknown script';
            }
        } catch (e) {
            description = 'script.describe() failed';
        }

        lastScript = lastScript
            .then(function () {
                var promise = script.execute();
                promise.then(function () {
                    console.debug('✓', description)
                });
                return promise;
            })
            .catch(function (error) {
                console.error('✘', description);
                if (error) {
                    console.log(error);
                }
            });
    });

    return lastScript.then(function () {
        return Promise.all(initializers).catch(function () {});
    });

};

// Capture insertBefore before it get's rewritten
var insertBefore = Element.prototype.insertBefore;
phast.ScriptsLoader.Utilities = function (document) {

    this._document = document;

    function executeString(string) {
        return new Promise(function (resolve, reject) {
            try {
                // See: http://perfectionkills.com/global-eval-what-are-the-options/
                (1,eval)(string);
            } catch (e) {
                console.error("[Phast] Error in eval'ed script:", e);
                console.log("First 100 bytes of script body:", string.substr(0, 100).replace(/\s+/g, ' '));
                reject(new Error("" + e));
            }
            resolve();
        })
    }

    function copyElement(source) {
        var copy = document.createElement(source.nodeName);
        Array.prototype.forEach.call(source.attributes, function (attr) {
            copy.setAttribute(attr.nodeName, attr.nodeValue);
        });
        return copy;
    }

    function restoreOriginals(element) {
        var shouldRemoveType = !element.hasAttribute('data-phast-original-type');
        element.removeAttribute('data-phast-params');
        Array.prototype
            .map.call(element.attributes, function (attr) {
                return attr.nodeName;
            })
            .forEach(function (attrName) {
                var matches = attrName.match(/^data-phast-original-(.*)/i);
                if (matches) {
                    element.setAttribute(matches[1], element.getAttribute(attrName));
                    element.removeAttribute(attrName);
                }
            });
        if (shouldRemoveType) {
            element.removeAttribute('type');
        }
    }

    function replaceElement(target, replacement) {
        return new Promise(function (resolve, reject) {
            var src = replacement.getAttribute('src');
            replacement.addEventListener('load', resolve);
            replacement.addEventListener('error', reject);
            replacement.removeAttribute('src');
            insertBefore.call(target.parentNode, replacement, target);
            target.parentNode.removeChild(target);
            if (src) {
                replacement.setAttribute('src', src);
            }
        });
    }

    function writeProtectAndExecuteString(sourceElement, scriptString) {
        return writeProtectAndCallback(sourceElement, function () {
            return executeString(scriptString);
        });
    }

    function writeProtectAndReplaceElement(target, replacement) {
        return writeProtectAndCallback(replacement, function () {
            return replaceElement(target, replacement);
        });
    }

    function writeProtectAndCallback(sourceElement, callback) {
        var writeBuffer = '';
        document.write = function (string) {
            writeBuffer += string;
        };
        document.writeln = function (string) {
            writeBuffer += string + '\n';
        };
        return callback()
            .then(function () {
                sourceElement.insertAdjacentHTML('afterend', writeBuffer);
            })
            .finally(function () {
                delete document.write;
                delete document.writeln;
            });
    }

    function addPreload(url) {
        var link = document.createElement('link');
        link.setAttribute('rel', 'preload');
        link.setAttribute('as', 'script');
        link.setAttribute('href', url);
        document.head.appendChild(link);
    }

    this.executeString = executeString;
    this.copyElement = copyElement;
    this.restoreOriginals = restoreOriginals;
    this.replaceElement = replaceElement;
    this.writeProtectAndExecuteString = writeProtectAndExecuteString;
    this.writeProtectAndReplaceElement = writeProtectAndReplaceElement;
    this.addPreload = addPreload;
};

phast.ScriptsLoader.Scripts = {};

phast.ScriptsLoader.Scripts.InlineScript = function (utils, element) {

    this._utils = utils;
    this._element = element;

    this.init = function () {
        return Promise.resolve();
    };

    this.execute = function () {
        var execString = element.textContent.replace(/^\s*<!--.*\n/i, '');
        utils.restoreOriginals(element);
        return utils.writeProtectAndExecuteString(element, execString);
    };

    this.describe = function () {
        return 'inline script';
    };
};

phast.ScriptsLoader.Scripts.AsyncBrowserScript = function (utils, element) {
    
    var resolver;

    this._utils = utils;
    this._element = element;

    this.init = function  () {
        utils.addPreload(getSrc(element));
        return new Promise(function (r) {
            resolver = r;
        });
    };

    this.execute = function () {
        var newElement = utils.copyElement(element);
        utils.restoreOriginals(newElement);
        utils.replaceElement(element, newElement)
            .then(resolver)
            .catch(resolver);
        return Promise.resolve();
    };

    this.describe = function() {
        return 'async script at ' + getSrc(element);
    };
};

phast.ScriptsLoader.Scripts.SyncBrowserScript = function (utils, element) {

    this._utils = utils;
    this._element = element;

    this.init = function () {
        utils.addPreload(getSrc(element));
        return Promise.resolve();
    };

    this.execute = function () {
        var newElement = utils.copyElement(element);
        utils.restoreOriginals(newElement);
        return utils.writeProtectAndReplaceElement(element, newElement);
    };

    this.describe = function () {
        return 'sync script at ' + getSrc(element);
    };

};

phast.ScriptsLoader.Scripts.AsyncAJAXScript = function (utils, element, fetch, fallback) {

    this._utils = utils;
    this._element = element;
    this._fetch = fetch;
    this._fallback = fallback;

    var load;
    var resolver;
    this.init = function () {
        load = fetch(element);
        return new Promise(function (r) {
            resolver = r;
        });
    };

    this.execute = function () {
        load
            .then(function (execString) {
                utils.restoreOriginals(element);
                return utils.executeString(execString).then(resolver);
            })
            .catch(function () {
                fallback.init();
                return fallback.execute().then(resolver);
            });
        return Promise.resolve();
    };

    this.describe = function () {
        return 'bundled async script at ' + JSON.parse(element.getAttribute('data-phast-params'))['src'];
    };
};

phast.ScriptsLoader.Scripts.SyncAJAXScript = function (utils, element, fetch, fallback) {

    this._utils = utils;
    this._element = element;
    this._fetch = fetch;
    this._fallback = fallback;

    var promise;
    this.init = function () {
        promise = fetch(element);
        return promise;
    };

    this.execute = function () {
        return promise
            .then(function (execString) {
                utils.restoreOriginals(element);
                utils.writeProtectAndExecuteString(element, execString);
            })
            .catch(function () {
                fallback.init();
                return fallback.execute();
            });
    };

    this.describe = function () {
        return 'bundled sync script at ' + JSON.parse(element.getAttribute('data-phast-params'))['src'];
    };
};

phast.ScriptsLoader.Scripts.Factory = function (document, fetch) {

    var Scripts = phast.ScriptsLoader.Scripts;

    var utils = new phast.ScriptsLoader.Utilities(document);

    this.makeScriptFromElement = function (element) {
        var fallback;

        if (isProxied(element)) {
            if (isAsync(element)) {
                fallback = new Scripts.AsyncBrowserScript(utils, element);
                return new Scripts.AsyncAJAXScript(utils, element, fetch, fallback);
            }
            fallback = new Scripts.SyncBrowserScript(utils, element);
            return new Scripts.SyncAJAXScript(utils, element, fetch, fallback);
        }

        if (isInline(element)) {
            return new Scripts.InlineScript(utils, element);
        }

        if (isAsync(element)) {
            return new Scripts.AsyncBrowserScript(utils, element);
        }

        return new Scripts.SyncBrowserScript(utils, element);
    };

    function isProxied(element) {
        return element.hasAttribute('data-phast-params');
    }

    function isInline (element) {
        return !element.hasAttribute('src');
    }

    function isAsync(element) {
        return element.hasAttribute('async');
    }

};

function getSrc(element) {
    if (element.hasAttribute('data-phast-original-src')) {
        return element.getAttribute('data-phast-original-src');
    } else if (element.hasAttribute('src')) {
        return element.getAttribute('src');
    }
}
