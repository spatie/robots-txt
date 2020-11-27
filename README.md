# Parse `robots.txt`, `robots` meta and headers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/robots-txt.svg?style=flat-square)](https://packagist.org/packages/spatie/robots-txt)
![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/robots-txt/run-tests?label=tests)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/robots-txt.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/robots-txt)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/robots-txt.svg?style=flat-square)](https://packagist.org/packages/spatie/robots-txt)
[![StyleCI](https://styleci.io/repos/122979707/shield?branch=master)](https://styleci.io/repos/122979707)

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
$robots = Robots::create();

$robots->mayIndex('https://www.spatie.be/nl/admin');

$robots->mayFollowOn('https://www.spatie.be/nl/admin');
```

You can also specify a user agent:

``` php
$robots = Robots::create('UserAgent007');
```

By default, `Robots` will look for a `robots.txt` file on `https://host.com/robots.txt`.
Another location can be specified like so:

``` php
$robots = Robots::create()
    ->withTxt('https://www.spatie.be/robots-custom.txt');

$robots = Robots::create()
    ->withTxt(__DIR__ . '/public/robots.txt');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Brent Roose](https://github.com/brendt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
