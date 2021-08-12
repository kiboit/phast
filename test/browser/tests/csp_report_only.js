test("csp_report_only.php", function (assert, document) {
  assert.deepEqual(
    document.defaultView.SCRIPTS,
    ["correct nonce", "incorrect nonce", "no nonce"],
    "All scripts should be executed"
  );
  assert.equal(
    document.defaultView.REPORTS,
    2,
    "Two reports should have been made"
  );
});
