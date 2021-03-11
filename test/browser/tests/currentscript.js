test("currentscript.php", function (assert, document) {
  if (!("currentScript" in document)) {
    return;
  }

  assert.equal(
    document.querySelector("#result").innerText,
    "QED",
    "The contents of the data-value attribute on the script should be retrieved"
  );

  var a = document.createElement("a");
  a.href = "currentscript.script.js";
  assert.equal(
    document.querySelector("#src").innerText,
    a.href,
    "The value of the src attribute should match the original"
  );
});
