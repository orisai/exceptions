# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/orisai/exceptions/compare/1.1.1...HEAD)

- Composer
	- allows PHP 8.2

## [1.1.1](https://github.com/orisai/exceptions/compare/1.1.0...1.1.1) - 2022-11-03

### Fixed

- `Message`
  - `with()` parameter `$title` expects non-empty-string

## [1.1.0](https://github.com/orisai/exceptions/compare/1.0.0...1.1.0) - 2021-07-22

### Added

- `Message`
	- Configurable line length via `Message::$lineLength`
	- `with('title', 'content')` for custom fields
- `ConfigurableException`
	- Suppressed exceptions
		- `withSuppressed()`, `getSuppressed()`
		- Suppressed exceptions messages are appended to main exception message
			- Can be disabled by `ConfigurableException::$addSuppressedToMessage = false;`

### Removed

- `Message`
	- Public properties `$context`, `$problem`, `$solution`
	  (technically a BC break, but in a never documented and impractical to use feature)

## [1.0.0](https://github.com/orisai/exceptions/releases/tag/1.0.0) - 2020-11-10

### Added

- `CheckedException` interface
  - base `DomainException`
- `UncheckedException` interface
  - base `LogicalException`
  - `Deprecated`
  - `InvalidArgument`
  - `InvalidState`
  - `MemberInaccessible`
  - `NotImplemented`
  - `ShouldNotHappen`
- `ConfigurableException` trait
