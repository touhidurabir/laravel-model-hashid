# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 11, 12 and 13 support added.
- PHPUnit 10, 11 and 12 support for tests; test methods now use `#[Test]` attributes alongside the `@test` annotations.
- Fixed `LogicException` on recent Laravel versions caused by instantiating the model while it was being booted in `IdHashable::bootIdHashable`.
- Fixed `hashid:run` command on Laravel 10+ by using `dispatchSync` (with `dispatchNow` fallback) for the non-queued run.
- Fixed PHP 8.4 implicit nullable parameter deprecations in `IdHashable::canHaveHashId`, `Hasher::__construct`, `Hasher::setPadding` and `CommandExceptionHandler::outputConsoleException`.
- Code style formatted to PSR-12 with Laravel Pint (`composer format` / `composer lint`).
- Replaced Travis CI with GitHub Actions (PHP 8.1 × Laravel 8/9/10 and PHP 8.2–8.4 × Laravel 11/12/13, plus a code style check).
- Fixed `decode_hashid()` / `decode_hashids()` ignoring the `hasher.alphabets` config, which also affected the `DehashRouteParams` and `DehashRequestParams` middlewares. **Behaviour change:** apps with a custom alphabet now decode hash ids correctly instead of getting the hash string back.
- Added the `ModelHashid` facade alias. The `ModelUuid` alias is kept for backwards compatibility but is deprecated (it collides with laravel-model-uuid) and will be removed in the next major release.
- Allowed `hashids/hashids` 5.x (`^4.1||^5.0`). A `Hasher` without a padding now passes `0` to Hashids instead of `null`, which 5.x rejects. Generated hashes are identical on 4.x and 5.x.
- `hashid:run` now returns an explicit success exit code instead of `null`.
- Added a `bootstrap/app.php` middleware registration example for Laravel 11+ to the README.
- Tests: the static hash id generation flag is reset between tests, so the suite passes in random order.


## [1.0.0] - 2021-08-29
- Initial release

## [1.1.0] - 2021-09-20
- Bug fix.
- Added hash id regenration command feature.
- Tests update and new tests added.
- Readme update.