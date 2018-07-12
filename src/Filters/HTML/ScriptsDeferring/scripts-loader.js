var Promise = phast.ES6Promise;

phast.ScriptsLoader = {};

phast.ScriptsLoader.ScriptFactory = function (fetch) {

    this.makeFromElement = function (element) {

    };

};

phast.ScriptsLoader.ProxiedSyncScript = function (script, fetch) {

    var loadPromise = new Promise(function () {});

    if (isProxied()) {
        loadPromise = fetch(script.getAttribute('src'));
    }

    function isProxied() {
        if (!script.hasAttribute('src')) {
            return false;
        }
        if (!script.hasAttribute('data-phast-original-src')) {
            return false;
        }
        return script.getAttribute('src') !== script.getAttribute('data-phast-original-src');
    }

    function execute() {
        return loadPromise
            .then(function (scriptText) {
                try {
                    // See: http://perfectionkills.com/global-eval-what-are-the-options/
                    (1,eval)(scriptText);
                } catch (e) {
                    console.error("[Phast] Error in inline script:", e);
                    console.log("First 100 bytes of script body:", scriptText.substr(0, 100));
                    throw e;
                }
            });
    }

    this.isProxied = isProxied;
    this.execute = execute;
};

phast.ScriptsLoader.ProxiedAsyncScript = function (script, fetch) {};

phast.ScriptsLoader.BrowserSyncScript = function (script, fetch) {};

phast.ScriptsLoader.BrowserAsyncScript = function (script, fetch) {};
