# Parse `robots.txt`, `robots` meta and headers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/robots-txt.svg?style=flat-square)](https://packagist.org/packages/spatie/robots-txt)
[![Tests](https://github.com/spatie/robots-txt/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/robots-txt/actions/workflows/run-tests.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/robots-txt.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/robots-txt)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/robots-txt.svg?style=flat-square)](https://packagist.org/packages/spatie/robots-txt)

Determine if a page may be crawled from robots.txt, robots meta tags and robot headers.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/robots-txt.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/robots-txt)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/robots-txt
```

## Usage

``` php
$robots = Spatie\Robots\Robots::create();

$robots->mayIndex('https://www.spatie.be/nl/admin');

$robots->mayFollowOn('https://www.spatie.be/nl/admin');

$robotsTxt = new RobotsTxt('
  User-agent: *
  Disallow: /admin
  Crawl-delay: 1.5
');
$robotsTxt->allows('/admin', 'google'); // false
$robotsTxt->whyDisallows('/admin', 'google')[0]->userAgent; // '*'
$robotsTxt->crawlDelay('/admin', '*'); // '1.5'
```

You can also specify a user agent:

``` php
$robots = Spatie\Robots\Robots::create('UserAgent007');
```

By default, `Robots` will look for a `robots.txt` file on `https://host.com/robots.txt`.
Another location can be specified like so:

``` php
$robots = Spatie\Robots\Robots::create()
    ->withTxt('https://www.spatie.be/robots-custom.txt');

$robots = Spatie\Robots\Robots::create()
    ->withTxt(__DIR__ . '/public/robots.txt');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Brent Roose](https://github.com/brendt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
