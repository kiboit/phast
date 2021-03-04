test("stylesheet_large.php", function (assert, document) {
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
      "rgb(242, 222, 222)",
      "The <h1> should have a reddish background"
    );

    var styles = document.querySelectorAll("style");
    assert.equal(
      styles.length,
      1,
      "There should be one style element on the page"
    );
    assert.ok(
      styles[0].textContent.length >= 120 * 1000,
      "The stylesheet in <style> should be at least 120K"
    );
  }
});
