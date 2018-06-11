# Changelog


## [1.5.3] - 2018-06-11

### Added
* Phast now sends the `Expires` header, in addition to `Cache-Control`, so that
  mod_expires doesn't add its own.

### Fixed
* Phast now correctly locates resources on setups where DOCUMENT_ROOT is wrong,
  but SCRIPT_NAME and SCRIPT_FILENAME are congruent.


## [1.5.2] - 2018-05-29

### Fixed
* Inline scripts that begin with `<!--` now work on IE.


## [1.5.1] - 2018-05-23

### Fixed
* The `Content-Encoding: identity` header is no longer sent.
* The bundler request is now flushed before it gets larger than 4.5K or so.


## [1.5.0] - 2018-05-11

### Added
* Only optimized versions of images are now inlined.

### Fixed
* We do not rely on `finfo` for determining file types anymore.
* Non-cached non-local styles won't cause a flicker on first load anymore.


## [1.4.0] - 2018-05-03

### Added
* Support for the Requests library that is bundled by WordPress.

### Fixed
* The bundler service does now not fail entirely when cURL is missing and remote
  resources are requested.


## [1.3.2] - 2018-05-03

### Fixed
* Phast is no longer dependent on the ctype extension.
* A regression on IE 11 due to a missing `Promise` implementation was fixed.
* URL parsing no longer fails on malformed URLs. (For PhastPress.)


## [1.3.1] - 2018-04-27

### Fixed
* Phast now works when Fileinfo extension is not installed.


## [1.3.0] - 2018-04-18

### Added
* Attributes with JSON values are now quoted with single quotes for better
  readability.

### Fixed
* Phast now works on Windows.


## [1.2.0] - 2018-04-13

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
  missing. This was fixed. ([#60])

[#60]: https://github.com/kiboit/phast/issues/60


## [1.1.0] - 2018-04-12

### Added
* Inlining of small images in HTML, CSS.
* CSS request bundling.
* First byte time optimization.
* `<base>` tag support.
* X-Accel-Expires header.

### Changed
* HTML processing using a regex-based tokenizer, rather than DOMDocument ([#29]).
* Cache garbage collection is improved and sets a hard limit on the cache size.

### Fixed
* IFrame lazy loading compatibility with already existing implementations.

[#29]: https://github.com/kiboit/phast/pull/29


[Unreleased]: https://github.com/kiboit/phast/compare/1.5.3...master
[1.5.3]: https://github.com/kiboit/phast/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/kiboit/phast/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/kiboit/phast/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/kiboit/phast/compare/1.4.0...1.5.0
[1.4.0]: https://github.com/kiboit/phast/compare/1.3.2...1.4.0
[1.3.2]: https://github.com/kiboit/phast/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/kiboit/phast/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/kiboit/phast/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/kiboit/phast/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/kiboit/phast/compare/1.0.0...1.1.0
