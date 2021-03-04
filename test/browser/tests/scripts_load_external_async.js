test("scripts_load_external_async.php", function (assert, document) {
  assert.equal(
    document.defaultView.jQuerySrc,
    "https://code.jquery.com/jquery-3.3.1.slim.min.js",
    "jQuery src is correct"
  );
  assert.notOk(
    document.defaultView.jQueryLoaded,
    "jQuery was loaded before sync script"
  );

  wait(
    assert,
    function () {
      return document.defaultView.onLoadCalledAfterJQuery !== undefined;
    },
    function () {
      assert.ok(
        document.defaultView.onLoadCalledAfterJQuery,
        "window.onload called after jQuery load"
      );
    }
  );
});
