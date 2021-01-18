(function () {
  // https://github.com/filamentgroup/woff2-feature-test/blob/master/woff2.js
  var supportsWoff2 = (function () {
    if (!("FontFace" in window)) {
      return false;
    }

    var f = new FontFace(
      "t",
      'url( "data:font/woff2;base64,d09GMgABAAAAAADwAAoAAAAAAiQAAACoAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAABmAALAogOAE2AiQDBgsGAAQgBSAHIBuDAciO1EZ3I/mL5/+5/rfPnTt9/9Qa8H4cUUZxaRbh36LiKJoVh61XGzw6ufkpoeZBW4KphwFYIJGHB4LAY4hby++gW+6N1EN94I49v86yCpUdYgqeZrOWN34CMQg2tAmthdli0eePIwAKNIIRS4AGZFzdX9lbBUAQlm//f262/61o8PlYO/D1/X4FrWFFgdCQD9DpGJSxmFyjOAGUU4P0qigcNb82GAAA" ) format( "woff2" )',
      {}
    );
    f.load()["catch"](function () {});

    return f.status == "loading" || f.status == "loaded";
  })();

  if (supportsWoff2) {
    return;
  }

  console.log(
    "[Phast] Browser does not support WOFF2, falling back to original stylesheets"
  );

  Array.prototype.forEach.call(
    document.querySelectorAll("style[data-phast-ie-fallback-url]"),
    function (el) {
      var link = document.createElement("link");
      if (el.hasAttribute("media")) {
        link.setAttribute("media", el.getAttribute("media"));
      }
      link.setAttribute("rel", "stylesheet");
      link.setAttribute("href", el.getAttribute("data-phast-ie-fallback-url"));
      el.parentNode.insertBefore(link, el);
      el.parentNode.removeChild(el);
    }
  );

  Array.prototype.forEach.call(
    document.querySelectorAll("style[data-phast-nested-inlined]"),
    function (groupEl) {
      groupEl.parentNode.removeChild(groupEl);
    }
  );
})();
