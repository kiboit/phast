test("rewrite_image_in_inline_style.php", function (assert, document) {
  var divs = document.getElementsByTagName("div");
  assert.equal(divs.length, 1, "There is one <DIV> element on the page");
  var match = /url\("?(.+?)"?\)/.exec(divs[0].style.backgroundImage);
  assert.ok(
    match,
    "We can parse the url() in the <DIV>'s background-image style"
  );
  var url = match[1];
  assert.ok(
    /phast\.php/.test(url),
    "The <DIV>'s background image is loaded through phast.php"
  );

  // Load URL as image and check size
  async(assert, function (done) {
    var img = new Image();
    img.onload = function () {
      done();
      assert.equal(img.width, 256, "The image's width is as expected");
      assert.equal(img.height, 256, "The image's height is as expected");
    };
    img.onerror = function () {
      done();
      assert.ok(false, "The image loads");
    };
    img.src = url;
  });

  // Load URL as string and check type
  async(assert, function (done) {
    retrieve(url, function (content) {
      done();
      assert.ok(content.length > 0, "The image data is not empty");
      assert.ok(/^.PNG/.test(content), "The image data has a PNG signature");
    });
  });
});
