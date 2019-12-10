[![Latest Version](https://img.shields.io/github/tag/olivertappin/phpcs-diff.svg?style=flat&label=release)](https://github.com/olivertappin/phpcs-diff/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Build Status](https://travis-ci.org/olivertappin/phpcs-diff.svg?branch=master)](https://travis-ci.org/olivertappin/phpcs-diff)
[![Quality Score](https://img.shields.io/scrutinizer/g/olivertappin/phpcs-diff.svg?style=flat)](https://scrutinizer-ci.com/g/olivertappin/phpcs-diff)
[![GitHub issues](https://img.shields.io/github/issues/olivertappin/phpcs-diff.svg)](https://github.com/olivertappin/phpcs-diff/issues)
[![Total Downloads](https://img.shields.io/packagist/dt/olivertappin/phpcs-diff.svg?style=flat)](https://packagist.org/packages/olivertappin/phpcs-diff)

## Installation

The recommended method of installing this library is via [Composer](https://getcomposer.org/).

### Composer

#### Global Installation

Run the following command from your project root:

    composer global require olivertappin/phpcs-diff

#### Manual Installation

Alternatively, you can manually include a dependency for `olivertappin/phpcs-diff` in your `composer.json` file. For example:

```json
{
    "require-dev": {
        "olivertappin/phpcs-diff": "^1.0"
    }
}
```

And run `composer update olivertappin/phpcs-diff`.

### Git Clone

You can also download the `phpcs-diff` source and create a symlink to your `/usr/bin` directory:

    git clone https://github.com/olivertappin/phpcs-diff.git
    ln -s phpcs-diff/bin/phpcs-diff /usr/bin/phpcs-diff
    cd /var/www/project
    phpcs-diff master -v

## Usage

### Basic Usage

```shell
phpcs-diff <current-branch> <base-branch> -v
```

Where the current branch you are on is the branch you are comparing with, and `develop` is the base branch. In this example, `phpcs-diff` would run the following diff statement behind the scenes:

```shell
git diff my-current-branch develop
```

_Please note:_
- The `-v` flag is optional. This returns a verbose output during processing.
- The `current-branch` parameter is optional. If this is not defined, phpcs-diff will use the current commit hash via `git rev-parse --verify HEAD`.
- You must have a `ruleset.xml` defined in your project base directory.

After running `phpcs-diff`, the executable will return an output similar to the following:

```
########## START OF PHPCS CHECK ##########
module/Poject/src/Console/Script.php
 - Line 28 (WARNING) Line exceeds 120 characters; contains 190 characters
 - Line 317 (ERROR) Blank line found at end of control structure
########### END OF PHPCS CHECK ###########
```

Currently this is the only supported format however, I will look into adding additional formats (much like `phpcs`) in the near future.

### Travis CI Usage

To use this as part of your CI/CD pipeline, create a script with the following:

```bash
#!/bin/bash
set -e
if [ ! -z "$TRAVIS_PULL_REQUEST_BRANCH" ]; then
  git fetch `git config --get remote.origin.url` $TRAVIS_BRANCH\:refs/remotes/origin/$TRAVIS_BRANCH;
  composer global require olivertappin/phpcs-diff;
  ~/.composer/vendor/bin/phpcs-diff $TRAVIS_BRANCH;
else
  echo "This test does not derive from a pull-request."
  echo "Unable to run phpcs-diff (as there's no diff)."

  # Here you might consider running phpcs instead:
  # composer global require squizlabs/php_codesniffer;
  # ~/.composer/vendor/bin/phpcs .
fi;
```

Which will allow you to run `phpcs-diff` against the diff of your pull-request.

Here's a sample of how this might look within Travis CI:

![Travis CI Example](https://user-images.githubusercontent.com/9773040/70551339-43bcfc00-1b6f-11ea-90c7-bc660e8dea28.png)

## About
`phpcs-diff` detects violations of a defined set of coding standards based on a `git diff`. It uses `phpcs` from the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) project.

This project helps by achieving the following:
- Speeds up your CI/CD pipeline validating changed files only, rather than the whole code base.
- Allows you to migrate legacy code bases that cannot risk changing everything at once to become fully compliant to a coding standard.

This executable works by only checking the changed lines, compared to the base branch, against all failed violations for those files, so you can be confident that any new or changed code will be compliant.

This will hopefully put you in a position where your codebase will become more compliant to that coding standard over time, and maybe you will find the resource to eventually change everything, and just run `phpcs` on its own.

## Requirements

The latest version of `phpcs-diff` requires PHP version 5.6.0 or later.

This project also depends on `squizlabs/php_codesniffer` which is used internally to fetch the failed violations via `phpcs`.

Finally, the `league/climate` package is also installed. This is to deal with console output, but this dependency may be removed in a future release.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for information.
