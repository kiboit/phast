test("currentscript_async.php", function (assert, document) {
  if (!("currentScript" in document)) {
    return;
  }

  assert.equal(
    document.defaultView.OK.phast,
    10,
    "phast script should have seen `phast' 10 times"
  );

  assert.equal(
    document.defaultView.OK.async,
    10,
    "async script should have seen `async' 10 times"
  );
});
