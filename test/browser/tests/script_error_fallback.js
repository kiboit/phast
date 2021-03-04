test("script_error_fallback.php", function (assert, document) {
  assert.ok(
    document.defaultView.doesNotExistSrc,
    "window.doesNotExistSrc is defined"
  );
  assert.equal(
    document.defaultView.doesNotExistSrc,
    "does-not-exist.js",
    "window.doesNotExistSrc is not correct"
  );
});
