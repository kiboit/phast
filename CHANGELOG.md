# Changelog

## [Unreleased]

### Added
* Removal of `Content-Length` header when filters are applied.
* Inlined CSS from `maxcdn.bootstrapcdn.com`.

## [1.1.0]

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


[Unreleased]: https://github.com/kiboit/phast/compare/1.1.0...master
[1.1.0]: https://github.com/kiboit/phast/compare/1.0.0...1.1.0
[#29]: https://github.com/kiboit/phast/pull/29
