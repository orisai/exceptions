# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/orisai/exceptions/compare/1.0.0...HEAD)

### Added

- `Message`
	- Configurable line length via `Message::$lineLength`
	- `with('title', 'content')` for custom fields

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
