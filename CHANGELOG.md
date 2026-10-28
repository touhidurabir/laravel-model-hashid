# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 11, 12 and 13 support added.
- PHPUnit 10, 11 and 12 support for tests; test methods now use `#[Test]` attributes alongside the `@test` annotations.
- Fixed `LogicException` on recent Laravel versions caused by instantiating the model while it was being booted in `IdHashable::bootIdHashable`.
- Fixed `hashid:run` command on Laravel 10+ by using `dispatchSync` (with `dispatchNow` fallback) for the non-queued run.
- Fixed PHP 8.4 implicit nullable parameter deprecations in `IdHashable::canHaveHashId`, `Hasher::__construct`, `Hasher::setPadding` and `CommandExceptionHandler::outputConsoleException`.


## [1.0.0] - 2021-08-29
- Initial release

## [1.1.0] - 2021-09-20
- Bug fix.
- Added hash id regenration command feature.
- Tests update and new tests added.
- Readme update.