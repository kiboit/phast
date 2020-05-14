# Changelog

## 1.46 - 2020-05-14

* Whitelist `cdnjs.cloudflare.com` for CSS processing.


## 1.45 - 2020-05-13

* Use `font-display: block` for icon fonts (currently Font Awesome,
  GeneratePress and Dashicons).


## 1.44 - 2020-05-04

* Support `data-pagespeed-no-defer` and `data-cfasync="false"` attributes on
  scripts for disabling script deferral (in addition to `data-phast-no-defer`).
* Leave `data-{phast,pagespeed}-no-defer` and `data-cfasync` attributes in place
  to aid debugging.


## 1.43 - 2020-04-30

* Base64 encode the config JSON passed to the frontend, to stop Gtranslate or
  other tools from mangling the service URL that is contained in it.


## 1.42 - 2020-04-15

* Speed up script load, and fix a bug with setTimeout functions running before
  the next script is loaded.


## 1.41 - 2020-04-02

* Support compressed external resources (ie, proxied styles and scripts).


## 1.40 - 2020-04-02

* Add s.pinimg.com, google-analytics.com/gtm/js to script proxy whitelist.


## 1.39 - 2020-03-27

* Remove blob script only after load.  This fixes issues with scripts sometimes
  not running in Safari.


## 1.38 - 2020-03-26

* Fixed a regression causing external scripts to be executed out of order.


## 1.37 - 2020-03-26

* Execute scripts by inserting a `<script>` tag with a blob URL, instead of
  using global eval, so that global variables defined in strict-mode scripts are
  globally visible.


## 1.36 - 2020-03-22

* Clean any existing output buffer, instead of flushing it, before starting
  Phast output buffer.


## 1.35 - 2020-03-20

* Use all service parameters for hash-based cache marker.


## 1.34 - 2020-03-19

* Add the option to cancel processing by Phast by calling cancel() on the
  OutputBufferHandler returned from PhastDocumentFilters::deploy().


## 1.33 - 2020-03-12

* Stop proxying dynamically inserted scripts after onload hits.
* Combine the hash-based cache marker with the original modification time-based
  cache marker.


## 1.32 - 2020-03-09

* Remove comment tags (`<!-- ... -->`) from inline scripts.
* Send `Content-Length` header for images.


## 1.31 - 2020-03-05

* Use hash-based cache marker (see last release) when local files are addressed
  with a query string.


## 1.30 - 2020-03-05

* Change CSS cache marker when dependencies (eg, images) change.  This prevents
  showing old images because CSS referencing an old optimized version is cached.


## 1.29 - 2020-01-23

* Trick mod_security into accepting script proxy requests by replacing
  `src=http://...` with `src=hxxp://...`.


## 1.28 - 2020-01-22

* Regression fix: Send `Vary: Accept` for JPEGs that could be WebPs.


## 1.27 - 2020-01-22

* Don't send WebP images via Cloudflare.  Cloudflare [does not support `Vary:
  Accept`](https://serverfault.com/questions/780882/impossible-to-serve-webp-images-using-cloudflare), so sending WebP via Cloudflare can cause browsers that don't support
  WebP to download the wrong image type.  [Use Cloudflare Polish
  instead.](https://support.cloudflare.com/hc/en-us/articles/360000607372-Using-Cloudflare-Polish-to-compress-images)


## 1.26 - 2020-01-22

* Keep `id` attributes on `style` elements.


## 1.25 - 2020-01-20

* Keep newlines when minifying HTML.


## 1.24 - 2020-01-16

* Send Content-Security-Policy and X-Content-Type-Options headers on resources
  to speculatively prevent any XSS attacks via MIME sniffing.


## 1.23 - 2019-11-12

* Make CSS filters configurable using switches.


## 1.22 - 2019-10-20

* Remove empty media queries from optimize CSS.
* Use token to refer to bundled resources, to shorten URL length.
* Clean up server-side statistics.
* Add HTML minification (whitespace removal).
* Add inline JavaScript and JSON minification (whitespace removal).
* Add a build system to generate a single PHP file with minified scripts.


## 1.21 - 2019-08-26

* Don't attempt to optimize CSS selectors containing parentheses, avoiding a bug
  removing applicable :not(.class) selectors.


## 1.20 - 2019-06-30

* Use valid value for script `type` to quiet W3C validator.


## 1.19 - 2019-06-13

* Add *.typekit.net, stackpath.bootstrapcdn.com to CSS whitelist.
* Don't apply rot13 on url-encoded characters.


## 1.18 - 2019-03-04

* Don't rewrite page-relative fragment image URLs like `fill:
  url(#destination)`.


## 1.17 - 2019-01-31

* Restore `script` attributes in sorted order (that is, `src` before `type`) to
  stop Internet Explorer from running scripts twice when they have `src` and
  `type` set.


## 1.16 - 2019-01-08

* Encode bundler request query to avoid triggering adblockers.
* Use a promise to delay bundler requests until the end of the event loop,
  rather than setTimeout.


## 1.15 - 2019-01-03

### Fixed
* Scripts can now be loaded via `document.write`. This restores normal browser
  behaviour.


## 1.14 - 2019-01-03

### Fixed
* `document.write` now immediately inserts the HTML into the page. This fixes
  compatibility with Google AdSense.


## 1.13.1 - 2018-12-09

### Fixed
* Remove query strings from the URLs passed to the JS, CSS bundler.


## 1.13.0 - 2018-12-09

### Added
* Remove query strings from URLs to stylesheets and scripts loaded from the
  local server. It is redundant, since we add the modification time to the URL
  ourselves.


## 1.12.2 - 2018-10-16

### Fixed
* Increase timeouts for API connection.


## 1.12.1 - 2018-10-15

### Fixed
* Don't use IndexedDB-backed cache on Safari.


## 1.12.0 - 2018-10-15

### Added
* Rewrite `data-lazy-src`, `data-lazy-srcset` attributes on `img`, `picture >
  source` tags.


## 1.11.0 - 2018-10-09

### Added
* Proxy CSS for maxcdn.bootstrapcdn.com, idangero.us, *.github.io.
* Proxy icon fonts and other resources from fonts.googleapis.com.
* Improve log messages from image filter.

### Fixed
* Do not proxy maps.googleapis.com, to fix NotLoadingAPIFromGoogleMapError.


## 1.10.0 - 2018-09-24

### Removed
* Moved image processing filters to API.


## 1.9.6 - 2018-09-13

### Fixed
* Removed `src` attribute from scripts that are loaded through the bundler, so
  that old versions of Firefox do not make extraneous downloads.


## 1.9.5 - 2018-09-13

### Fixed
* Check that the bundler returns the right amount of responses.

### Added
* Per-script debugging message when executing scripts.


## 1.9.4 - 2018-09-13

### Fixed
* Animated GIFs are no longer processed, so that animation is preserved.


## 1.9.3 - 2018-08-07

### Fixed
* `<!--` comments in inline scripts are removed only at the beginning.


## 1.9.2 - 2018-07-27

### Fixed
* Empty scripts are cached correctly.


## 1.9.1 - 2018-07-27

### Fixed
* Async scripts are now not loaded before sync scripts that occur earlier in the
  document.


## 1.9.0 - 2018-07-26

### Added
* Scripts are now retrieved in a single request.
* Non-existent filter classes are ignored, and an error is logged.
* A 'dummy filename' such as `__p__.js` is appended to service requests to trick
  Cloudflare into caching those responses.

### Fixed
* The maximum document size for filters to be applied was corrected to be 1 MiB,
  not 1 GiB


## 1.8.0 - 2018-07-11

### Added
* Bundle URLs are now much shorter, allowing more resources per request.
* Add `font-display: swap` to `@font-face` elements for immediate text
  rendering.

### Fixed
* Changed cache size threshold from 100 GiB to 500 MiB.


## 1.7.0 - 2018-07-04

### Added
* Support for `<PICTURE>` elements.
* `retrieverMap` path prefixes are now regexes.
* Bundle the `Requests` library and Mozilla CA certificates and use them as
  default HTTP client engine.


## 1.6.0 - 2018-06-27

### Added
* A configuration variable for toggling HTML document detection before applying filters.

### Fixed
* Unify the filter application logic when doing output buffering and on-demand application.


## 1.5.6 - 2018-06-25

### Fixed

* Reverted REQUEST_URI parsing to determine PATH_INFO.
* Process HTML where one or more comments occur before the doctype declaration.


## 1.5.5 - 2018-06-18

### Fixed
* ~~Phast will now fallback to using REQUEST_URI if DOCUMENT_URI is not
  available.~~ Reverted in 1.5.6.
* ~~Phast will now use full DOCUMENT_URI or REQUEST_URI if PHP_SELF is not part
  of them.~~ Reverted in 1.5.6.


## 1.5.4 - 2018-06-14

### Fixed
* An empty response from the image optimization API is now considered an error.


## 1.5.3 - 2018-06-11

### Added
* Phast now sends the `Expires` header, in addition to `Cache-Control`, so that
  mod_expires doesn't add its own.

### Fixed
* Phast now correctly locates resources on setups where DOCUMENT_ROOT is wrong,
  but SCRIPT_NAME and SCRIPT_FILENAME are congruent.


## 1.5.2 - 2018-05-29

### Fixed
* Inline scripts that begin with `<!--` now work on IE.


## 1.5.1 - 2018-05-23

### Fixed
* The `Content-Encoding: identity` header is no longer sent.
* The bundler request is now flushed before it gets larger than 4.5K or so.


## 1.5.0 - 2018-05-11

### Added
* Only optimized versions of images are now inlined.

### Fixed
* We do not rely on `finfo` for determining file types anymore.
* Non-cached non-local styles won't cause a flicker on first load anymore.


## 1.4.0 - 2018-05-03

### Added
* Support for the Requests library that is bundled by WordPress.

### Fixed
* The bundler service does now not fail entirely when cURL is missing and remote
  resources are requested.


## 1.3.2 - 2018-05-03

### Fixed
* Phast is no longer dependent on the ctype extension.
* A regression on IE 11 due to a missing `Promise` implementation was fixed.
* URL parsing no longer fails on malformed URLs. (For PhastPress.)


## 1.3.1 - 2018-04-27

### Fixed
* Phast now works when Fileinfo extension is not installed.


## 1.3.0 - 2018-04-18

### Added
* Attributes with JSON values are now quoted with single quotes for better
  readability.

### Fixed
* Phast now works on Windows.


## 1.2.0 - 2018-04-13

### Added
* `PhastDocumentFilters::apply()` method for integration in view rendering.
* Removal of `Content-Length` header when filters are applied.
* Inlined CSS from `maxcdn.bootstrapcdn.com`.
* Cross-domain requests to the service are allowed. (`Access-Control-Allow-Origin: *`)
* Cache control and other default headers for CSS bundler service.
* Processing of multiple images in one CSS rule. (`background: url(...), url(...)`)
* Proxy Google Maps API JS, DoubleClick stats JS

### Fixed
* Path format queries are now serialized the same way as normal queries (via
  `http_build_query()`). `urlencode()` serializes `false` differently, breaking
  token verification.
* An error was thrown during image processing when pngquant or jpegtran were
  missing. This was fixed.


## 1.1.0 - 2018-04-12

### Added
* Inlining of small images in HTML, CSS.
* CSS request bundling.
* First byte time optimization.
* `<base>` tag support.
* X-Accel-Expires header.

### Changed
* HTML processing using a regex-based tokenizer, rather than DOMDocument.
* Cache garbage collection is improved and sets a hard limit on the cache size.

### Fixed
* IFrame lazy loading compatibility with already existing implementations.
