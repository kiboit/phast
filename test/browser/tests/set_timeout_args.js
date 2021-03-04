test("set_timeout_args.php", function (assert, document) {
  assert.equal(document.defaultView.ARGA, "Hello");
  assert.equal(document.defaultView.ARGB, "World");
});
