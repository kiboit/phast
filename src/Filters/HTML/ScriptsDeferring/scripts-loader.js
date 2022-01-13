/* globals phast */

var Promise = phast.ES6Promise;

var hasCurrentScript = !!document.currentScript;

phast.ScriptsLoader = {};

phast.ScriptsLoader.getScriptsInExecutionOrder = function (document, factory) {
  var elements = Array.prototype.slice
    .call(document.querySelectorAll('script[type="text/phast"]'))
    .filter(allowExecution);

  var nonDeferred = [],
    deferred = [];

  for (var i = 0; i < elements.length; i++) {
    if (getSrc(elements[i]) !== undefined && isDefer(elements[i])) {
      deferred.push(elements[i]);
    } else {
      nonDeferred.push(elements[i]);
    }
  }

  return nonDeferred.concat(deferred).map(function (element) {
    return factory.makeScriptFromElement(element);
  });

  function allowExecution(script) {
    try {
      var csp = phast.config.scriptsLoader.csp;
    } catch (e) {
      return true;
    }

    if (csp.nonce == null) {
      return true;
    }

    if (script.nonce === csp.nonce) {
      return true;
    }

    try {
      reportScript(csp, script);
    } catch (e) {
      console.error("Could not send CSP report due to error:", e);
    }

    if (csp.reportOnly) {
      console.warn(
        "Script with missing or invalid nonce would not be executed (but report-only mode is enabled):",
        script
      );

      return true;
    }

    console.warn(
      "Script with missing or invalid nonce will not be executed:",
      script
    );

    return false;
  }

  function reportScript(csp, script) {
    var report = {
      "blocked-uri": getSrc(script),
      disposition: csp.reportOnly ? "report" : "enforce",
      "document-uri": location.href,
      referrer: document.referrer,
      "script-sample": getSample(script),
      implementation: "phast",
    };

    try {
      script.dispatchEvent(
        new SecurityPolicyViolationEvent("securitypolicyviolation", {
          blockedURI: report["blocked-uri"],
          disposition: report["disposition"],
          documentURI: report["document-uri"],
          effectiveDirective: "script-src-elem",
          originalPolicy: "phast",
          referrer: report["referrer"],
          sample: report["script-sample"],
          statusCode: 200,
          violatedDirective: "script-src-elem",
        })
      );
    } catch (e) {
      console.error(
        "[Phast] Could not dispatch securitypolicyviolation event",
        e
      );
    }

    if (!csp.reportUri) {
      return;
    }

    var body = { "csp-report": report };

    fetch(csp.reportUri, {
      method: "POST",
      headers: {
        "Content-Type": "application/csp-report",
      },
      credentials: "same-origin",
      redirect: "error",
      keepalive: true,
      body: JSON.stringify(body),
    });
  }

  function getSample(script) {
    if (!script.hasAttribute("src")) {
      return script.textContent.substr(0, 40);
    }
  }
};

phast.ScriptsLoader.executeScripts = function (scripts) {
  var initializers = scripts.map(function (script) {
    return script.init();
  });

  var lastScript = Promise.resolve();

  scripts.forEach(function (script) {
    lastScript = phast.ScriptsLoader.chainScript(lastScript, script);
  });

  return lastScript.then(function () {
    return Promise.all(initializers).catch(function () {});
  });
};

phast.ScriptsLoader.chainScript = function (lastScript, script) {
  var description;

  try {
    if (script.describe) {
      description = script.describe();
    } else {
      description = "unknown script";
    }
  } catch (e) {
    description = "script.describe() failed";
  }

  return lastScript
    .then(function () {
      var promise = script.execute();
      promise.then(function () {
        console.debug("✓", description);
      });
      return promise;
    })
    .catch(function (error) {
      console.error("✘", description);
      if (error) {
        console.log(error);
      }
    });
};

// Capture insertBefore before it get's rewritten
var insertBefore = window.Element.prototype.insertBefore;
phast.ScriptsLoader.Utilities = function (document) {
  this._document = document;

  var completionCallbacks = 0;

  function executeString(string) {
    return new Promise(function (resolve) {
      var callbackName = "PhastCompleteScript" + ++completionCallbacks;

      var el = createScript(string);
      var trailer = createScript(callbackName + "()");

      window[callbackName] = next;
      document.body.appendChild(el);
      document.body.appendChild(trailer);

      function next() {
        resolve();
        document.body.removeChild(el);
        document.body.removeChild(trailer);
        delete window[callbackName];
      }
    });
  }

  function createScript(content) {
    var el = document.createElement("script");
    el.textContent = content;
    el.nonce = phast.config.scriptsLoader.csp.nonce;
    return el;
  }

  function copyElement(source) {
    var copy = document.createElement(source.nodeName);
    Array.prototype.forEach.call(source.attributes, function (attr) {
      copy.setAttribute(attr.nodeName, attr.nodeValue);
    });
    return copy;
  }

  function restoreOriginals(element) {
    element.removeAttribute("data-phast-params");
    var attrs = {};
    Array.prototype.map
      .call(element.attributes, function (attr) {
        return attr.nodeName;
      })
      .map(function (attrName) {
        var matches = attrName.match(/^data-phast-original-(.*)/i);
        if (matches) {
          attrs[matches[1].toLowerCase()] = element.getAttribute(attrName);
          element.removeAttribute(attrName);
        }
      });
    Object.keys(attrs)
      .sort()
      .map(function (attr) {
        element.setAttribute(attr, attrs[attr]);
      });
    if (!("type" in attrs)) {
      element.removeAttribute("type");
    }
  }

  function replaceElement(target, replacement) {
    return new Promise(function (resolve, reject) {
      var src = replacement.getAttribute("src");
      replacement.addEventListener("load", resolve);
      replacement.addEventListener("error", reject);
      replacement.removeAttribute("src");
      insertBefore.call(target.parentNode, replacement, target);
      target.parentNode.removeChild(target);
      if (src) {
        replacement.setAttribute("src", src);
      }
    });
  }

  function writeProtectAndExecuteString(sourceElement, scriptString) {
    return writeProtectAndCallback(sourceElement, function () {
      return emulateCurrentScript(sourceElement, function () {
        return executeString(scriptString);
      });
    });
  }

  function writeProtectAndReplaceElement(target, replacement) {
    return writeProtectAndCallback(replacement, function () {
      return replaceElement(target, replacement);
    });
  }

  function writeProtectAndCallback(sourceElement, callback) {
    var insertBefore = sourceElement.nextElementSibling;
    var lastScript = Promise.resolve();

    var ignoreType;
    if (isAsync(sourceElement)) {
      ignoreType = "async";
    } else if (isDefer(sourceElement)) {
      ignoreType = "defer";
    }

    document.write = function (string) {
      if (ignoreType) {
        console.warn(
          "document.write call from " + ignoreType + " script ignored"
        );
        return;
      }
      insert(string);
    };

    document.writeln = function (string) {
      if (ignoreType) {
        console.warn(
          "document.writeln call from " + ignoreType + " script ignored"
        );
        return;
      }
      insert(string + "\n");
    };

    function insert(string) {
      var container = document.createElement("div");
      container.innerHTML = string;
      var scripts = getExecutableScriptsInElement(container);
      if (
        insertBefore &&
        insertBefore.parentNode !== sourceElement.parentNode
      ) {
        insertBefore = sourceElement.nextElementSibling;
      }
      while (container.firstChild) {
        sourceElement.parentNode.insertBefore(
          container.firstChild,
          insertBefore
        );
      }
      scripts.map(executeScript);
    }

    function getExecutableScriptsInElement(container) {
      return Array.prototype.slice
        .call(container.getElementsByTagName("script"))
        .filter(function (script) {
          var type = script.getAttribute("type");
          return !type || /^(text|application)\/javascript(;|$)/i.test(type);
        });
    }

    function executeScript(scriptElement) {
      var scriptsFactory = new phast.ScriptsLoader.Scripts.Factory(document);
      var script = scriptsFactory.makeScriptFromElement(scriptElement);
      lastScript = phast.ScriptsLoader.chainScript(lastScript, script);
    }

    return callback()
      .then(function () {
        return lastScript;
      })
      .finally(function () {
        delete document.write;
        delete document.writeln;
      });
  }

  function emulateCurrentScript(sourceElement, fn) {
    if (hasCurrentScript) {
      try {
        Object.defineProperty(document, "currentScript", {
          configurable: true,
          get: function () {
            return sourceElement;
          },
        });
      } catch (e) {
        console.error(
          "[Phast] Unable to override document.currentScript on this browser: ",
          e
        );
      }
    }

    return fn().finally(function () {
      if (hasCurrentScript) {
        delete document.currentScript;
      }
    });
  }

  function addPreload(url) {
    var link = document.createElement("link");
    link.setAttribute("rel", "preload");
    link.setAttribute("as", "script");
    link.setAttribute("href", url);
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
    var execString = element.textContent.replace(/^\s*<!--.*\n/i, "");
    utils.restoreOriginals(element);
    return utils.writeProtectAndExecuteString(element, execString);
  };

  this.describe = function () {
    return "inline script";
  };
};

phast.ScriptsLoader.Scripts.AsyncBrowserScript = function (utils, element) {
  var resolver;

  this._utils = utils;
  this._element = element;

  this.init = function () {
    utils.addPreload(getSrc(element));
    return new Promise(function (r) {
      resolver = r;
    });
  };

  this.execute = function () {
    var newElement = utils.copyElement(element);
    utils.restoreOriginals(newElement);
    utils.replaceElement(element, newElement).then(resolver).catch(resolver);
    return Promise.resolve();
  };

  this.describe = function () {
    return "async script at " + getSrc(element);
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
    return "sync script at " + getSrc(element);
  };
};

phast.ScriptsLoader.Scripts.AsyncAJAXScript = function (
  utils,
  element,
  fetch,
  fallback
) {
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
        return utils
          .writeProtectAndExecuteString(element, execString)
          .then(resolver);
      })
      .catch(function () {
        fallback.init();
        return fallback.execute().then(resolver);
      });
    return Promise.resolve();
  };

  this.describe = function () {
    return (
      "bundled async script at " +
      element.getAttribute("data-phast-original-src")
    );
  };
};

phast.ScriptsLoader.Scripts.SyncAJAXScript = function (
  utils,
  element,
  fetch,
  fallback
) {
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
        return utils.writeProtectAndExecuteString(element, execString);
      })
      .catch(function () {
        fallback.init();
        return fallback.execute();
      });
  };

  this.describe = function () {
    return (
      "bundled sync script at " +
      element.getAttribute("data-phast-original-src")
    );
  };
};

phast.ScriptsLoader.Scripts.Factory = function (document, fetch) {
  var Scripts = phast.ScriptsLoader.Scripts;

  var utils = new phast.ScriptsLoader.Utilities(document);

  this.makeScriptFromElement = function (element) {
    var fallback;

    if (
      element.getAttribute("data-phast-debug-force-method") &&
      window.location.host.match(/\.test$/)
    ) {
      return new Scripts[element.getAttribute("data-phast-debug-force-method")](
        utils,
        element
      );
    }

    if (isProxied(element)) {
      if (isAsync(element)) {
        fallback = new Scripts.AsyncBrowserScript(utils, element);
        return fetch
          ? new Scripts.AsyncAJAXScript(utils, element, fetch, fallback)
          : fallback;
      }
      fallback = new Scripts.SyncBrowserScript(utils, element);
      return fetch
        ? new Scripts.SyncAJAXScript(utils, element, fetch, fallback)
        : fallback;
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
    return element.hasAttribute("data-phast-params");
  }

  function isInline(element) {
    return !element.hasAttribute("src");
  }
};

function getSrc(element) {
  if (element.hasAttribute("data-phast-original-src")) {
    return element.getAttribute("data-phast-original-src");
  } else if (element.hasAttribute("src")) {
    return element.getAttribute("src");
  }
}

function isAsync(element) {
  return (
    element.hasAttribute("async") || element.hasAttribute("data-phast-async")
  );
}

function isDefer(element) {
  return (
    element.hasAttribute("defer") || element.hasAttribute("data-phast-defer")
  );
}
