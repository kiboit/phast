var Promise = phast.ES6Promise;

phast.ResourceLoader.instance = phast.ResourceLoader.make(
  phast.config.resourcesLoader.serviceUrl,
  phast.config.resourcesLoader.shortParamsMappings,
  phast.config.resourcesLoader.pathInfo
);

phast.forEachSelectedElement = function (selector, callback) {
  Array.prototype.forEach.call(
    window.document.querySelectorAll(selector),
    callback
  );
};

phast.once = function (fn) {
  var done = false;
  return function () {
    if (!done) {
      done = true;
      fn.apply(this, Array.prototype.slice(arguments));
    }
  };
};

phast.on = function (obj, evt) {
  return new Promise(function (resolve) {
    obj.addEventListener(evt, resolve);
  });
};

phast.wait = function (delay) {
  return new Promise(function (resolve) {
    setTimeout(resolve, delay);
  });
};

phast.on(document, "DOMContentLoaded").then(function () {
  var statsLines, statsElement;

  function isStatsElement(element) {
    return (
      element &&
      element.nodeType === 8 &&
      /^\s*\[Phast\]/.test(element.textContent)
    );
  }

  function searchForStats(inspected) {
    while (inspected) {
      if (isStatsElement(inspected)) {
        return inspected;
      }
      inspected = inspected.nextSibling;
    }
    return false;
  }

  statsElement = searchForStats(document.documentElement.nextSibling);
  if (statsElement === false) {
    statsElement = searchForStats(document.body.firstChild);
  }

  if (statsElement) {
    statsLines = statsElement.textContent.replace(/^\s+|\s+$/g, "").split("\n");
    console.groupCollapsed(statsLines.shift());
    console.log(statsLines.join("\n"));
    console.groupEnd();
  }
});

phast.on(document, "DOMContentLoaded").then(function () {
  var t = performance.timing;
  var m = [];
  m.push(["Downloading phases:"]);
  m.push([
    "  Look up hostname in DNS            + %s ms",
    fmt(t.domainLookupEnd - t.fetchStart),
  ]);
  m.push([
    "  Establish connection               + %s ms",
    fmt(t.connectEnd - t.domainLookupEnd),
  ]);
  m.push([
    "  Send request                       + %s ms",
    fmt(t.requestStart - t.connectEnd),
  ]);
  m.push([
    "  Receive first byte                 + %s ms",
    fmt(t.responseStart - t.requestStart),
  ]);
  m.push([
    "  Download page                      + %s ms",
    fmt(t.responseEnd - t.responseStart),
  ]);
  m.push([""]);
  m.push(["Totals:"]);
  m.push([
    "  Time to first byte                   %s ms",
    fmt(t.responseStart - t.fetchStart),
  ]);
  m.push([
    "    (since request start)              %s ms",
    fmt(t.responseStart - t.requestStart),
  ]);
  m.push([
    "  Total request time                   %s ms",
    fmt(t.responseEnd - t.fetchStart),
  ]);
  m.push([
    "    (since request start)              %s ms",
    fmt(t.responseEnd - t.requestStart),
  ]);
  m.push([" "]);
  var f = [];
  var p = [];
  m.forEach(function (i) {
    f.push(i.shift());
    p = p.concat(i);
  });
  console.groupCollapsed("[Phast] Client-side performance metrics");
  console.log.apply(console, [f.join("\n")].concat(p));
  console.groupEnd();
  function fmt(v) {
    v = "" + v;
    while (v.length < 4) {
      v = " " + v;
    }
    return v;
  }
});
