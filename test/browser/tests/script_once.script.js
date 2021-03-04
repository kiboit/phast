window.COUNT = (window.COUNT || 0) + 1;
console.log("Script triggered (" + window.COUNT + ")");

setTimeout(function () {
  var s = document.createElement("script");
  s.src = "script_once.done.js?" + Date.now();
  document.body.appendChild(s);
});
