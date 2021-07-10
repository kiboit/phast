test("csp.php", function (assert, document) {
  assert.equal(
    document.defaultView.SCRIPTS,
    ["correct nonce"],
    "Only the script with the (correct) nonce should be executed"
  );
});
