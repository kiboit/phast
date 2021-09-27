(function () {
  var h1 = document.createElement("h1");
  h1.appendChild(document.createTextNode("Hi from module!"));
  document.body.insertBefore(h1, document.body.firstChild);
})();
