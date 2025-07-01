# Changelog

All notable changes to `robots-txt` will be documented in this file

## 2.5.1 - 2025-07-01

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot in https://github.com/spatie/robots-txt/pull/68
* fix: fix warnings and avoid unused variables by @chapeupreto in https://github.com/spatie/robots-txt/pull/71

### New Contributors

* @chapeupreto made their first contribution in https://github.com/spatie/robots-txt/pull/71

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.5.0...2.5.1

## 2.5.0 - 2025-05-23

### What's Changed

* Implement RobotsTxt->whyDisallows($path, $userAgent) to find why a path was disallowed by @gvlasov in https://github.com/spatie/robots-txt/pull/66

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.4.0...2.5.0

## 2.4.0 - 2025-05-22

### What's Changed

* Bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/spatie/robots-txt/pull/63
* Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot in https://github.com/spatie/robots-txt/pull/64
* Implement crawl-delay directive for robots.txt by @gvlasov in https://github.com/spatie/robots-txt/pull/65

### New Contributors

* @gvlasov made their first contribution in https://github.com/spatie/robots-txt/pull/65

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.3.1...2.4.0

## 2.3.1 - 2025-01-31

### What's Changed

* Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/spatie/robots-txt/pull/62
* RobotsTxt: add missing class property - allowsPerUserAgent by @DavidGoodwin in https://github.com/spatie/robots-txt/pull/61

### New Contributors

* @DavidGoodwin made their first contribution in https://github.com/spatie/robots-txt/pull/61

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.3.0...2.3.1

## 2.3.0 - 2025-01-27

### What's Changed

* Update phpunit/phpunit requirement from ^9.0|^10.0 to ^11.5.2 by @dependabot in https://github.com/spatie/robots-txt/pull/59
* "Allow" Handling by @Dwarfex in https://github.com/spatie/robots-txt/pull/60

### New Contributors

* @Dwarfex made their first contribution in https://github.com/spatie/robots-txt/pull/60

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.2.3...2.3.0

## 2.2.3 - 2024-10-22

### What's Changed

* avoid expensive queries that do not change certain results by @elcapo in https://github.com/spatie/robots-txt/pull/53

### New Contributors

* @elcapo made their first contribution in https://github.com/spatie/robots-txt/pull/53

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.2.2...2.2.3

## 2.2.2 - 2024-09-25

### What's Changed

* Bump actions/checkout from 2 to 4 by @dependabot in https://github.com/spatie/robots-txt/pull/47
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/spatie/robots-txt/pull/48
* Fixes "case-sensitive" URI matching for Disallow rules in robots.txt by @mattfo0 in https://github.com/spatie/robots-txt/pull/46

### New Contributors

* @dependabot made their first contribution in https://github.com/spatie/robots-txt/pull/47
* @mattfo0 made their first contribution in https://github.com/spatie/robots-txt/pull/46

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.2.1...2.2.2

## 2.2.1 - 2024-08-09

### What's Changed

* Add missing use statement for InvalidArgumentException by @remcom in https://github.com/spatie/robots-txt/pull/44

### New Contributors

* @remcom made their first contribution in https://github.com/spatie/robots-txt/pull/44

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.2.0...2.2.1

## 2.2.0 - 2024-04-22

### What's Changed

* optionally allow partial matches and global groups by @resohead in https://github.com/spatie/robots-txt/pull/43

### New Contributors

* @resohead made their first contribution in https://github.com/spatie/robots-txt/pull/43

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.1.0...2.2.0

## 2.1.0 - 2024-04-19

### What's Changed

* Add some efficiencies to prevent unnecessary requests by @tsjason in https://github.com/spatie/robots-txt/pull/42

### New Contributors

* @tsjason made their first contribution in https://github.com/spatie/robots-txt/pull/42

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.0.3...2.1.0

## 2.0.3 - 2023-11-22

### What's Changed

- Update .gitattributes and ignore php_cs cache by @angeljqv in https://github.com/spatie/robots-txt/pull/36
- Update PHPUnit config to latest schema by @patinthehat in https://github.com/spatie/robots-txt/pull/39
- Add PHP 8.2 Support by @patinthehat in https://github.com/spatie/robots-txt/pull/38
- Fix deprecation message by @buismaarten in https://github.com/spatie/robots-txt/pull/41

### New Contributors

- @angeljqv made their first contribution in https://github.com/spatie/robots-txt/pull/36
- @buismaarten made their first contribution in https://github.com/spatie/robots-txt/pull/41

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.0.2...2.0.3

## 2.0.2 - 2022-05-18

## What's Changed

- Match meta tag for single quotes, often used by Wordpress sites by @gjportegies in https://github.com/spatie/robots-txt/pull/35

## New Contributors

- @gjportegies made their first contribution in https://github.com/spatie/robots-txt/pull/35

**Full Changelog**: https://github.com/spatie/robots-txt/compare/2.0.1...2.0.2

## 2.0.1 - 2021-05-06

- added x-robots-tag: none (#32)

## 2.0.0 - 2021-03-28

- require PHP 8+
- drop support for PHP 7.x
- convert syntax to PHP 8
- remove deprecated methods
- use php-cs-fixer & github workflow

## 1.0.10 - 2020-12-08

- handle multiple user-agent (#29)

## 1.0.9 - 2020-11-27

- add support for PHP 8.0 + move to GitHub actions (#27)

## 1.0.8 - 2020-09-12

- make user agent checks case-insensitive

## 1.0.7 - 2020-04-29

- fix find robots meta tag line if minified code (#23)

## 1.0.6 - 2020-04-07

- fix headers checking (nofollow, noindex) for custom userAgent (#21)

## 1.0.5 - 2019-08-08

- improvements around handling of wildcards, end-of-string, query string

## 1.0.4 - 2019-08-07

- improve readability

## 1.0.3 - 2019-03-11

- fix parsing robotstxt urls with keywords (#14)

## 1.0.2 - 2019-01-11

- make robots.txt check case insensitive

## 1.0.1 - 2018-05-07

- prevent exception if the domain has no robots.txt

## 1.0.0 - 2018-05-07

- initial release
