var ScriptsLoader = phast.ScriptsLoader,
  Promise = phast.ES6Promise;

QUnit.module("ScriptsLoader", function () {
  QUnit.module("Scripts", function (hooks) {
    var Scripts = ScriptsLoader.Scripts;

    var fetch = function (element, successful) {
      var params = JSON.parse(element.getAttribute("data-phast-params"));
      utils._pushCall("fetch", params.src);
      return new Promise(function (resolve, reject) {
        if (successful) {
          resolve("contents-for-" + params.src);
        } else {
          reject("failure-for-" + params.src);
        }
      });
    };

    var successfulFetch = function (element) {
      return fetch(element, true);
    };

    var failingFetch = function (element) {
      return fetch(element, false);
    };

    var fallback = {
      init: function () {
        utils._pushCall("fallbackInit");
      },

      execute: function () {
        utils._pushCall("fallbackExecute");
        return Promise.resolve("fallback-promise");
      },
    };

    var utils, element;
    hooks.beforeEach(function () {
      element = document.createElement("script");
      utils = new UtilsMock();
    });

    QUnit.module("InlineScript", function (hooks) {
      var script, whenInitialized;
      hooks.beforeEach(function () {
        script = new Scripts.InlineScript(utils, element);
        whenInitialized = script.init();
      });

      QUnit.test("No init execution", function (assert) {
        assertEmptyInit(assert, whenInitialized);
      });

      QUnit.test("Execute", function (assert) {
        var done = getAsync(assert);
        element.innerHTML = ' <!-- stuff here \nconsole.log("works");';
        script.execute().then(function () {
          assertNumberOfCalls(assert, 2);
          assertRestoredOriginals(assert, 0, element);
          assertWriteProtectedStringExecution(
            assert,
            1,
            element,
            'console.log("works");'
          );
          done();
        });
      });
    });

    QUnit.module("AsyncBrowserScript", function (hooks) {
      var script, whenInitialized;
      hooks.beforeEach(function () {
        script = new Scripts.AsyncBrowserScript(utils, element);
        whenInitialized = script.init();
      });

      QUnit.test("Init execution", function (assert) {
        assertNumberOfCalls(assert, 1);
        assertPreloadAdded(assert, 0, element.getAttribute("src"));
        assertInitResolved(assert, false, whenInitialized);
      });

      QUnit.test("Test execution", function (assert) {
        var done = getAsync(assert);
        script.execute().then(function (testText) {
          assertNumberOfCalls(assert, 4);
          var copy = assertElementCopied(assert, 1, element);
          assertRestoredOriginals(assert, 2, copy);
          assertReplacedElement(assert, 3, element, copy);
          assertPromiseNotFromFunction(assert, testText);
          done();
        });
        assertInitResolved(assert, true, whenInitialized);
      });
    });

    QUnit.module("SyncBrowserScript", function (hooks) {
      var script, whenInitialized;
      hooks.beforeEach(function () {
        script = new Scripts.SyncBrowserScript(utils, element);
        whenInitialized = script.init();
      });

      QUnit.test("Test adding preload on init", function (assert) {
        var done = getAsync(assert);
        whenInitialized.then(function () {
          assertNumberOfCalls(assert, 1);
          assertPreloadAdded(assert, 0, element.getAttribute("src"));
          done();
        });
      });

      QUnit.test("Test execution", function (assert) {
        var done = getAsync(assert);
        script.execute().then(function (testText) {
          var copy = assertElementCopied(assert, 1, element);
          assertRestoredOriginals(assert, 2, copy);
          assertWriteProtectedReplacedElement(assert, 3, element, copy);
          assertPromiseText(assert, testText, "writeProtectAndReplaceElement");
          done();
        });
      });
    });

    QUnit.module("AsyncAJAXScript", function (hooks) {
      var whenInitialized;
      hooks.beforeEach(function () {
        whenInitialized = null;
        element.setAttribute("data-phast-params", '{"src": "proxied-url"}');
      });

      function makeScript(fetch) {
        var script = new Scripts.AsyncAJAXScript(
          utils,
          element,
          fetch,
          fallback
        );
        whenInitialized = script.init();
        return script;
      }

      QUnit.test("Test init load execution", function (assert) {
        makeScript(successfulFetch);
        assertNumberOfCalls(assert, 1);
        assertCallToFetch(assert, 0);
        assertInitResolved(assert, false, whenInitialized);
      });

      QUnit.test("Test execution method waits on init", function (assert) {
        assert.timeout(200);
        var done = assert.async(2);
        makeScript(successfulFetch)
          .execute()
          .then(function (testText) {
            assertPromiseNotFromFunction(assert, testText);
            done();
          });
        window.setTimeout(function () {
          assertRestoredOriginals(assert, 1, element);
          assertWriteProtectedStringExecution(
            assert,
            2,
            element,
            "contents-for-proxied-url"
          );
          assertInitResolved(assert, true, whenInitialized);
          done();
        }, 20);
      });

      QUnit.test("Test fallback", function (assert) {
        var done = getAsync(assert);
        makeScript(failingFetch).execute();
        window.setTimeout(function () {
          assertNumberOfCalls(assert, 3);
          assertFallbackInitCall(assert, 1);
          assertFallbackExecuteCall(assert, 2);
          done();
        }, 20);
      });
    });

    QUnit.module("SyncAJAXScript", function (hooks) {
      var whenInitialized;
      hooks.beforeEach(function () {
        whenInitialized = null;
        element.setAttribute("data-phast-params", '{"src": "proxied-url"}');
      });

      function makeScript(fetch) {
        var script = new Scripts.SyncAJAXScript(
          utils,
          element,
          fetch,
          fallback
        );
        whenInitialized = script.init();
        return script;
      }

      QUnit.test("Test init start loading", function (assert) {
        var done = getAsync(assert);
        makeScript(successfulFetch);
        assertNumberOfCalls(assert, 1);
        assertCallToFetch(assert, 0);
        whenInitialized.then(function () {
          assertNumberOfCalls(assert, 1);
          done();
        });
      });

      QUnit.test("Test execution", function (assert) {
        var done = getAsync(assert);
        makeScript(successfulFetch)
          .execute()
          .then(function () {
            assertNumberOfCalls(assert, 3);
            assertRestoredOriginals(assert, 1, element);
            assertWriteProtectedStringExecution(
              assert,
              2,
              element,
              "contents-for-proxied-url"
            );
            done();
          });
      });

      QUnit.test("Test fallback", function (assert) {
        var done = getAsync(assert);
        makeScript(failingFetch)
          .execute()
          .then(function (testText) {
            assertNumberOfCalls(assert, 3);
            assertFallbackInitCall(assert, 1);
            assertFallbackExecuteCall(assert, 2);
            assertFallbackPromise(assert, testText);
            done();
          });
      });
    });

    function assertEmptyInit(assert, whenInitialized) {
      assertNumberOfCalls(assert, 0);
      var done = getAsync(assert);
      whenInitialized.then(function () {
        assertNumberOfCalls(assert, 0);
        done();
      });
    }

    function assertNumberOfCalls(assert, expectedCallsCount) {
      assert.equal(
        utils._getCalls().length,
        expectedCallsCount,
        expectedCallsCount + " calls have been made"
      );
    }

    function assertRestoredOriginals(assert, idx, element) {
      var calls = utils._getCalls();
      assert.equal(
        calls[idx].name,
        "restoreOriginals",
        "Originals have been restored"
      );
      assert.ok(
        calls[idx].params === element,
        "The originals were restored to the right element"
      );
    }

    function assertElementCopied(assert, idx, element) {
      var calls = utils._getCalls();
      assert.equal(calls[idx].name, "copyElement", "Element was copied");
      assert.ok(calls[idx].params === element, "Correct element was copied");
      return calls[idx].return;
    }

    function assertReplacedElement(assert, idx, target, replacement) {
      var calls = utils._getCalls();
      assert.equal(calls[idx].name, "replaceElement", "Element was replaced");
      assert.ok(calls[idx].target === target, "Correct element was replaced");
      assert.ok(
        calls[idx].replacement === replacement,
        "Correct element was added"
      );
    }

    function assertWriteProtectedReplacedElement(
      assert,
      idx,
      target,
      replacement
    ) {
      var calls = utils._getCalls();
      assert.equal(
        calls[idx].name,
        "writeProtectAndReplaceElement",
        "Element was write protected and replaced"
      );
      assert.ok(calls[idx].target === target, "Correct element was replaced");
      assert.ok(
        calls[idx].replacement === replacement,
        "Correct element was added"
      );
    }

    function assertWriteProtectedStringExecution(
      assert,
      idx,
      target,
      execString
    ) {
      var calls = utils._getCalls();
      assert.equal(
        calls[idx].name,
        "writeProtectAndExecuteString",
        "String was write protected and executed"
      );
      assert.ok(calls[idx].target === target, "Correct element was protected");
      assert.ok(
        calls[idx].string === execString,
        "Correct string was executed"
      );
    }

    function assertPreloadAdded(assert, idx, url) {
      var calls = utils._getCalls();
      assert.equal(calls[idx].name, "addPreload", "Preload was added");
      assert.equal(calls[idx].params, url, "Correct url was preloaded");
    }

    function assertPromiseText(assert, testText, expectedFunc) {
      assert.equal(
        testText,
        expectedFunc + " promise",
        "Correct promise was returned"
      );
    }

    function assertPromiseNotFromFunction(assert, testText) {
      assert.ok(
        testText === undefined,
        "The promise is not from any util.func"
      );
    }

    function assertCallToFetch(assert, idx) {
      var calls = utils._getCalls();
      assert.equal(calls[idx].name, "fetch", "Fetch was called");
      assert.equal(calls[idx].params, "proxied-url", "Correct url was fetched");
    }

    function assertFallbackInitCall(assert, idx) {
      var calls = utils._getCalls();
      assert.equal(calls[idx].name, "fallbackInit", "Fallback has been called");
    }

    function assertFallbackExecuteCall(assert, idx) {
      var calls = utils._getCalls();
      assert.equal(
        calls[idx].name,
        "fallbackExecute",
        "Fallback has been called"
      );
    }

    function assertFallbackPromise(assert, testText) {
      assert.equal(testText, "fallback-promise", "This is a fallback promise");
    }

    function assertInitResolved(assert, shouldResolve, promise) {
      var done = getAsync(assert);
      var initResolved = false;
      promise.then(function () {
        initResolved = true;
      });
      setTimeout(function () {
        if (shouldResolve) {
          assert.ok(initResolved, "Init promise is resolved");
        } else {
          assert.notOk(initResolved, "Init promise is not resolved");
        }
        done();
      }, 10);
    }

    function getAsync(assert) {
      assert.timeout(100);
      return assert.async();
    }

    function UtilsMock() {
      var calls = [];

      this._pushCall = function (funcName, params) {
        calls.push({ name: funcName, params: params });
      };

      this._getCalls = function () {
        return calls;
      };

      this.restoreOriginals = function (element) {
        this._pushCall("restoreOriginals", element);
      };

      this.copyElement = function (element) {
        var returnElement = document.createElement("script");
        calls.push({
          name: "copyElement",
          params: element,
          return: returnElement,
        });
        return returnElement;
      };

      this.replaceElement = function (target, replacement) {
        calls.push({
          name: "replaceElement",
          target: target,
          replacement: replacement,
        });
        return Promise.resolve("replaceElement promise");
      };

      this.writeProtectAndReplaceElement = function (target, replacement) {
        calls.push({
          name: "writeProtectAndReplaceElement",
          target: target,
          replacement: replacement,
        });
        return Promise.resolve("writeProtectAndReplaceElement promise");
      };

      this.writeProtectAndExecuteString = function (target, string) {
        calls.push({
          name: "writeProtectAndExecuteString",
          target: target,
          string: string,
        });
        return Promise.resolve("writeProtectAndExecuteString promise");
      };

      this.executeString = function (string) {
        this._pushCall("executeString", string);
        return Promise.resolve("executeString promise");
      };

      this.addPreload = function (url) {
        this._pushCall("addPreload", url);
      };
    }
  });

  QUnit.module("Scripts.Factory", function (hooks) {
    var Scripts = ScriptsLoader.Scripts;

    var fetch = function () {};

    var factory = new Scripts.Factory(document, fetch);

    var element;
    hooks.beforeEach(function () {
      element = document.createElement("script");
    });

    QUnit.test("Test creating inline script", function (assert) {
      var script = factory.makeScriptFromElement(element);
      assert.ok(
        script instanceof Scripts.InlineScript,
        "Instance of InlineScript"
      );
      assertCorrectBuild(assert, script);
    });

    QUnit.test("Test creating AsyncBrowserScript", function (assert) {
      element.setAttribute("src", "some-src");
      element.setAttribute("async", "");

      var script = factory.makeScriptFromElement(element);
      assert.ok(
        script instanceof Scripts.AsyncBrowserScript,
        "Correct for only src"
      );
      assertCorrectBuild(assert, script);
    });

    QUnit.test("Test create SyncBrowserScript", function (assert) {
      element.setAttribute("src", "some-src");
      var script = factory.makeScriptFromElement(element);
      assert.ok(
        script instanceof Scripts.SyncBrowserScript,
        "Correct for only src"
      );
      assertCorrectBuild(assert, script);
    });

    QUnit.test("Test create AsyncAJAXScript", function (assert) {
      element.setAttribute("data-phast-params", "{}");
      element.setAttribute("data-phast-original-src", "original-src");
      element.setAttribute("async", "");

      var script = factory.makeScriptFromElement(element);
      assert.ok(script instanceof Scripts.AsyncAJAXScript, "Correct type");
      assert.ok(
        script._fallback instanceof Scripts.AsyncBrowserScript,
        "Correct fallback"
      );

      assertCorrectBuild(assert, script, true);
      assertCorrectBuild(assert, script._fallback);
    });

    QUnit.test("Test create SyncAJAXScript", function (assert) {
      element.setAttribute("data-phast-params", "{}");
      element.setAttribute("data-phast-original-src", "original-src");

      var script = factory.makeScriptFromElement(element);
      assert.ok(script instanceof Scripts.SyncAJAXScript, "Correct type");
      assert.ok(
        script._fallback instanceof Scripts.SyncBrowserScript,
        "Correct fallback"
      );

      assertCorrectBuild(assert, script, true);
      assertCorrectBuild(assert, script._fallback);
    });

    function assertCorrectBuild(assert, script, ajax) {
      assert.ok(
        script._utils._document === document,
        "Correct utils document set"
      );
      assert.ok(script._element === element, "Correct element set");
      if (ajax) {
        assert.ok(script._fetch === fetch, "Correct fetch");
      }
    }
  });

  QUnit.module("Test executeScripts()", function () {
    QUnit.test("Init all scripts", function (assert) {
      var init = 0;
      var scripts = [1, 2, 3].map(function () {
        return {
          init: function () {
            init++;
          },

          execute: function () {
            return Promise.resolve();
          },
        };
      });
      ScriptsLoader.executeScripts(scripts);
      assert.equal(init, 3, "All scripts initialized");
    });

    QUnit.test("Execute correctly", function (assert) {
      var order = [];
      var execs = [
        function () {
          order.push(0);
          return Promise.resolve();
        },
        function () {
          order.push(1);
          return Promise.reject();
        },
        function () {
          return new Promise(function (resolve) {
            window.setTimeout(function () {
              order.push(2);
              resolve();
            }, 50);
          });
        },
        function () {
          order.push(3);
          return Promise.resolve();
        },
      ];
      var scripts = execs.map(function (exec) {
        return {
          init: function () {
            return Promise.resolve();
          },
          execute: exec,
        };
      });

      assert.timeout(100);
      var done = assert.async(2);
      window.setTimeout(function () {
        assert.equal(order.length, 2, "Correct length in mid execution");
        assert.equal(order[0], 0, "Correct item 0");
        assert.equal(order[1], 1, "Correct item 1");
        done();
      }, 25);

      ScriptsLoader.executeScripts(scripts).then(function () {
        assert.equal(order.length, 4, "Correct length in end");
        assert.equal(order[2], 2, "Correct item 0");
        assert.equal(order[3], 3, "Correct item 1");
        done();
      });
    });

    QUnit.test("Resolve on both init and execute", function (assert) {
      var initialized = false,
        executed = false;
      var script = {
        init: function () {
          return new Promise(function (resolve) {
            window.setTimeout(function () {
              initialized = true;
              resolve();
            }, 30);
          });
        },

        execute: function () {
          return new Promise(function (resolve) {
            executed = true;
            resolve();
          });
        },
      };

      assert.timeout(100);
      var done = assert.async();
      ScriptsLoader.executeScripts([script]).then(function () {
        assert.ok(initialized, "Was initialized");
        assert.ok(executed, "Was executed");
        done();
      });
    });

    QUnit.test("Resolve when error in init", function (assert) {
      var script = {
        init: function () {
          return Promise.reject();
        },
        execute: function () {
          return Promise.resolve();
        },
      };
      assert.timeout(100);
      assert.expect(0);
      var done = assert.async();
      ScriptsLoader.executeScripts([script]).then(done);
    });
  });

  QUnit.test("Test finding scripts in order", function (assert) {
    var d = document.implementation.createHTMLDocument("");
    function makeElement(attributes, elementName) {
      elementName = elementName || "script";
      var e = d.createElement(elementName);
      for (var x in attributes) {
        e.setAttribute(x, attributes[x]);
      }
      d.body.appendChild(e);
      return e;
    }

    makeElement({ "non-phast": "" });
    makeElement({});
    makeElement({}, "p");
    var inlineDeferred = makeElement({ type: "text/phast", defer: "" });
    var deferred = makeElement({
      type: "text/phast",
      defer: "",
      src: "some-src",
    });
    var inline = makeElement({ type: "text/phast" });
    var external = makeElement({ type: "text/phast", async: "" });

    var factory = {
      makeScriptFromElement: function (element) {
        return { _element: element };
      },
    };

    var scripts = ScriptsLoader.getScriptsInExecutionOrder(d, factory);
    assert.equal(scripts.length, 4, "Correct number of scripts found");
    assert.ok(scripts[0]._element === inlineDeferred, "Correct item 0");
    assert.ok(scripts[1]._element === inline, "Correct item 0");
    assert.ok(scripts[2]._element === external, "Correct item 1");
    assert.ok(scripts[3]._element === deferred, "Correct item 2");
  });
});
