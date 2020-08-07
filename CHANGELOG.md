# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `CheckedException` interface
  - base `DomainException`
- `UncheckedException` interface
  - base `LogicalException`
  - `Deprecated`
  - `InvalidArgument`
  - `InvalidState`
  - `NotImplemented`
  - `ShouldNotHappen`
- `ConfigurableException` trait

[Unreleased]: https://github.com/orisai/exceptions/compare/...HEAD
