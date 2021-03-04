test("inline_js.php", function (assert, document) {
  assert.equal(
    document.defaultView.didLoad,
    true,
    "didLoad should be set to true by inline script"
  );
  assert.equal(
    document.defaultView.srcWasEmpty,
    true,
    "srcWasEmpty should be set to true by inline script"
  );
  assert.equal(
    document.defaultView.shouldBeGlobal,
    true,
    "shouldBeGlobal should be defined in global scope"
  );
});
