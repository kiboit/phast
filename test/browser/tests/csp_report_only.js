test("csp_report_only.php", function (assert, document) {
  assert.deepEqual(
    document.defaultView.SCRIPTS,
    ["correct nonce", "incorrect nonce", "no nonce"],
    "All scripts should be executed"
  );
});
