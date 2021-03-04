"use strict";
/*
 comment that we shouldn't see
 */
/*!
 * some license here
 * some license here
 */
var a;
/*
 comment that we shouldn't see
 */
console.log("Main JS Loaded");

console.log("Main minification");

var something = "yolo";

var injectedScript = document.createElement("script");
injectedScript.src = "js/injected.js";
document.body.appendChild(injectedScript);
