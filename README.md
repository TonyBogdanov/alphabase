# Base-Anything conversions for PHP

[![Latest Stable Version](https://poser.pugx.org/tonybogdanov/alphabase/v/stable)](https://packagist.org/packages/tonybogdanov/alphabase)
[![License](https://poser.pugx.org/tonybogdanov/alphabase/license)](https://packagist.org/packages/tonybogdanov/alphabase)
![Build](https://github.com/tonybogdanov/alphabase/workflows/build/badge.svg)
[![Coverage](http://tonybogdanov.github.io/alphabase/coverage.svg)](http://tonybogdanov.github.io/alphabase/index.html)

## Installation

```bash
composer require tonybogdanov/alphabase:^1.0
```

## Usage

Pass the string you want to convert as first argument, the alphabet (a string of unique characters) of the input as
second argument, and the alphabet of the output as third argument.

The conversion expects all input characters to be found within the input alphabet and will convert them into characters
found within the output alphabet.

If your input alphabet contains 32 characters and your output one contains 64, you are essentially converting from
base32 into base64.

Keep in mind that the conversions here have nothing to do with standard algorithms like `base64`, and so are not
interchangeable.

```php
// qett
echo \TonyBogdanov\Alphabase\Converter::convert( 'abacab', 'abc', 'qwerty' );

// abacab
echo \TonyBogdanov\Alphabase\Converter::convert( 'qett', 'qwerty', 'abc' );
```

## Caveat

It is not possible to unambiguously convert between bases without considering a special / padding character. Doing so,
however, will force you to use a set of characters as your input alphabet + an extra padding character outside the
alphabet, which will then make it impossible to convert from base256, since there will be no ASCII character available
to use as padding.

To fix this issue the package will assume the very first character of each alphabet as the padding character. When a
padding character is found at the beginning of your input string, it will be directly translated into the padding
character of your output alphabet 1:1 without any conversion. This is only valid for the beginning of your input up
until the very first occurrence of any other character. After that point the padding character will be converted like
the rest.

This effect means you will lose the compressive capabilities of conversions like base32 to base128 for example, so
keep that in mind.

A good consideration in regard to this is to always pick your alphabets such that the very first (padding) character
is always one that has the lowest chance of being found in input strings, or at least in their beginning.
