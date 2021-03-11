test("currentscript.php", function (assert, document) {
  if (!("currentScript" in document)) {
    return;
  }

  assert.equal(
    document.defaultView.SYNC_VALUE,
    "sync",
    "The sync script should see data-value=sync"
  );

  var a = document.createElement("a");
  a.href = "currentscript.script.js";
  assert.equal(
    document.defaultView.SYNC_SRC,
    a.href,
    "The sync script should see the original src"
  );

  assert.equal(
    document.defaultView.INLINE_VALUE,
    "inline",
    "The inline script should see data-value=inline"
  );
});
