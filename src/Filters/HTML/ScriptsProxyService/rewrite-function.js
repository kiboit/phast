/* global
       Element
       Node
       phast
*/

var config = phast.config["script-proxy-service"];
var urlPattern = /^(https?:)?\/\//;
var typePattern = /^\s*(application|text)\/(x-)?(java|ecma|j|live)script/i;
var cacheMarker = Math.floor(
  new Date().getTime() / 1000 / config.urlRefreshTime
);
var whitelist = compileWhitelistPatterns(config.whitelist);

phast.scripts.push(function () {
  overrideDOMMethod("appendChild");
  overrideDOMMethod("insertBefore");
});

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
      window.console &&
        window.console.log("Phast: Failed to compile pattern:", pattern);
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
  var proxy = function () {
    var postprocess = processNode(arguments[0]);
    var result = original.apply(this, arguments);
    postprocess();
    return result;
  };
  Element.prototype[name] = proxy;
  window.addEventListener("load", function () {
    if (Element.prototype[name] === proxy) {
      delete Element.prototype[name];
    }
  });
}

function processNode(el) {
  if (
    !el ||
    el.nodeType !== Node.ELEMENT_NODE ||
    el.tagName !== "SCRIPT" ||
    !urlPattern.test(el.src) ||
    (el.type && !typePattern.test(el.type)) ||
    el.src.substr(0, config.serviceUrl.length) === config.serviceUrl ||
    !checkWhitelist(el.src)
  ) {
    return function () {};
  }

  var originalSrc = el.src;

  el.src = phast.buildServiceUrl(config, {
    service: "scripts",
    src: originalSrc,
    cacheMarker: cacheMarker,
  });

  el.setAttribute("data-phast-rewritten", "");

  return function () {
    el.src = originalSrc;
  };
}
