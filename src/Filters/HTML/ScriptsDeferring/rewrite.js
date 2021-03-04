/* global phast */

var Promise = phast.ES6Promise;

var go = phast.once(loadScripts);

phast.on(document, "DOMContentLoaded").then(function () {
  if (phast.stylesLoading) {
    phast.onStylesLoaded = go;
    setTimeout(go, 4000);
  } else {
    Promise.resolve().then(go);
  }
});

var loadFiltered = false;

window.addEventListener("load", function (e) {
  if (!loadFiltered) {
    e.stopImmediatePropagation();
  }
  loadFiltered = true;
});

document.addEventListener("readystatechange", function (e) {
  if (document.readyState === "loading") {
    e.stopImmediatePropagation();
  }
});

var didSetTimeout = false;
var originalSetTimeout = window.setTimeout;

window.setTimeout = function (fn, delay) {
  if (!delay || delay < 0) {
    didSetTimeout = true;
  }
  return originalSetTimeout.apply(window, arguments);
};

function loadScripts() {
  var scriptsFactory = new phast.ScriptsLoader.Scripts.Factory(
    document,
    fetchScript
  );
  var scripts = phast.ScriptsLoader.getScriptsInExecutionOrder(
    document,
    scriptsFactory
  );
  if (scripts.length === 0) {
    return;
  }

  setReadyState("loading");

  phast.ScriptsLoader.executeScripts(scripts).then(restoreReadyState);
}

function setReadyState(state) {
  try {
    Object.defineProperty(document, "readyState", {
      configurable: true,
      get: function () {
        return state;
      },
    });
  } catch (e) {
    console.warn(
      "[Phast] Unable to override document.readyState on this browser: ",
      e
    );
  }
}

function restoreReadyState() {
  waitForTimeouts()
    .then(function () {
      setReadyState("interactive");
      triggerEvent(document, "readystatechange");
      return waitForTimeouts();
    })
    .then(function () {
      triggerEvent(document, "DOMContentLoaded");
      return waitForTimeouts();
    })
    .then(function () {
      delete document["readyState"];
      triggerEvent(document, "readystatechange");
      if (loadFiltered) {
        triggerEvent(window, "load");
      }
      loadFiltered = true;
    });

  function waitForTimeouts() {
    return new Promise(function (resolve) {
      (function retry(depth) {
        if (didSetTimeout && depth < 10) {
          didSetTimeout = false;
          originalSetTimeout.call(window, function () {
            retry(depth + 1);
          });
        } else {
          requestAnimationFrame(resolve);
        }
      })(0);
    });
  }
}

function triggerEvent(on, name) {
  var e = document.createEvent("Event");
  e.initEvent(name, true, true);
  on.dispatchEvent(e);
}

function fetchScript(element) {
  return phast.ResourceLoader.instance.get(
    phast.ResourceLoader.RequestParams.fromString(
      element.getAttribute("data-phast-params")
    )
  );
}
