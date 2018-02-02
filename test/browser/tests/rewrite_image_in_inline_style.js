test('rewrite_image_in_inline_style.php', function (assert, document) {
    var divs = document.getElementsByTagName('div');
    assert.equal(divs.length, 1, "There is one <DIV> element on the page");
    var match = /url\("?(.+?)"?\)/.exec(divs[0].style.backgroundImage);
    assert.ok(match, "We can parse the url() in the <DIV>'s background-image style")
    var url = match[1];
    assert.ok(/phast\.php/.test(url), "The <DIV>'s background image is loaded through phast.php");
    var done = assert.async();
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
