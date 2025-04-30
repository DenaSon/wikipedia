# Changelog

All notable changes to this project will be documented in this file.

---

## [v1.0.2] - 2025-04-30

### Added
- ✅ **PHPUnit Tests**: Added comprehensive unit tests for core Wikipedia API methods including `summary()`, `extract()`, `html()`, `text()`, `raw()`, `imageUrl()`, `infoBox()`, `categories()`, `search()`, `suggest()`, and `description()`.
- ✅ **Feature Tests**: Implemented feature-level tests with mocked HTTP responses to validate real-world usage of the package.
- 🧾 **phpDoc Annotations**: Added detailed PHPDoc comments for all public methods in the main interface and implementation classes to enhance IDE integration and static analysis support.

### Changed
- 🔄 Internal test suite refactored for readability and future scalability.
- 🧹 Cleaned and unified method signatures, removed redundant assertions.

### Fixed
- 🛠 Addressed test failures related to response structure inconsistencies.
- 🐞 Resolved edge case bugs in `text()` and `raw()` method parsing logic.

---

## [v1.0.1] - 2025-04-XX

*Initial stable version with basic Wikipedia API features.*

---

