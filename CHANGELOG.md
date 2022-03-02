# Changelog

All notable changes to `aws-sns` will be documented in this file

## [1.4.0] - 2022-03-02
### Added
- Added support for Laravel 9 [#21](https://github.com/laravel-notification-channels/aws-sns/pull/21);
- Added tests to check the package compatibility with all versions supported [#21](https://github.com/laravel-notification-channels/aws-sns/pull/21);
- Bumped dependencies to better support newer versions of PHP and Laravel [#21](https://github.com/laravel-notification-channels/aws-sns/pull/21).

## [1.3.0] - 2021-10-13
### Added
- Added support for Origination Number (PR #15);
- Installation instructions on the README to solve dependencies conflics (Issue #17).

## [1.2.1] - 2021-03-21
### Fixed
- Hard-coded credentials were not being passed correctly.

## [1.2.0] - 2020-09-08
### Added
- Added support for Laravel 8 (PR#11).

## [1.1.1] - 2020-03-08
### Added
- You can now specify the Sender ID for the message using the `sender` method (PR#4).

## [1.1.0] - 2020-03-06
### Added
- Added support for Laravel 7.

## [1.0.1] - 2020-01-22
### Changed
- The notification object is now passed to the `routeNotificationForSns` function (PR#4). 

## [1.0.0] - 2020-01-12
### Added
- Initial release
