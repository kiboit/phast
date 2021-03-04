test("stylesheet_import.php", function (assert, document) {
  var h1 = document.getElementsByTagName("h1")[0];
  var cs = document.defaultView.getComputedStyle(h1, null);
  var done = assert.async();
  wait();
  function wait() {
    if (document.querySelectorAll("style[data-phast-params]").length > 0) {
      return setTimeout(wait);
    }
    done();
    assert.equal(
      cs.backgroundColor,
      "rgb(0, 0, 255)",
      "The <h1> should have a blue background"
    );

    var importStatements = 0;
    [].forEach.call(document.getElementsByTagName("style"), function (style) {
      if (/@import/.test(style.textContent)) {
        importStatements++;
      }
    });
    assert.equal(
      importStatements,
      0,
      "There should be no @import statements in any <style> elements"
    );
  }
});
