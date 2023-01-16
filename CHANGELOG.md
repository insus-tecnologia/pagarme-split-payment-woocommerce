# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.3] - 2023-01-15
### Added
- Log to register calls to Pagar.me API

### Removed
- Log that recorded split payment rules before they were sent to Pagar.me

### Fixed
- Warnings and notices

## [1.2.2] - 2022-08-16
### Fixed
- Iteration over non iterable variables

## [1.2.1] - 2022-05-17
### Fixed
- Custom post types admin access control

## [1.2.0] - 2022-05-04
### Added
- Payment split now works with renewal payment from WooCommerce Subscriptions plugin

### Fixed
- Warning when there is no partners in the order log

## [1.1.0] - 2022-04-27
### Added
- Possibility to define fixed amount for a partner in the payment split

### Fixed
- Splited amounts when using a discount coupon in the order

## [1.0.2] - 2021-12-29
### Changed
- Restrict access to admin area only for roles that explicit this behavior

## [1.0.1] - 2020-05-23
### Fixed
- Avoid error on partner creation when no recipient data is submitted
- List only partners on products association field
