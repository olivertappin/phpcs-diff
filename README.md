[![Latest Version](https://img.shields.io/github/tag/olivertappin/phpcs-diff.svg?style=flat&label=release)](https://github.com/olivertappin/phpcs-diff/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Build Status](https://travis-ci.org/olivertappin/phpcs-diff.svg?branch=master)](https://travis-ci.org/olivertappin/phpcs-diff)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/olivertappin/phpcs-diff.svg?style=flat)](https://scrutinizer-ci.com/g/olivertappin/phpcs-diff/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/olivertappin/phpcs-diff.svg?style=flat)](https://scrutinizer-ci.com/g/olivertappin/phpcs-diff)
[![GitHub issues](https://img.shields.io/github/issues/olivertappin/phpcs-diff.svg)](https://github.com/olivertappin/phpcs-diff/issues)
[![Total Downloads](https://img.shields.io/packagist/dt/olivertappin/phpcs-diff.svg?style=flat)](https://packagist.org/packages/olivertappin/phpcs-diff)

## About
phpcs-diff detects violations of a defined set of coding standards based on a git diff. It uses phpcs from the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) project.

This project is for those who have legacy code bases that cannot risk changing everything at once to become fully compliant to a coding standard. This executable works by only checking the changed lines, compared to the base branch, against all failed violations for those files, so you can be confident that any new or changed code will be compliant.

This will hopefully put you in a position where your codebase will become more compliant to that coding standard over time, and maybe you will find the resource to eventually change everything, and just run `phpcs` on its own.

## Usage

    NAME
           phpcs-diff - detect violations based on a git diff
    
    SYNOPSIS
           phpcs-diff [BASE_BRANCH]... [OPTION]...
    
    OPTIONS
           Here is a (very) short summary of the options available in phpcs-diff.
           
           -v
                  increase verbosity
                                   
Basic example

```shell
phpcs-diff develop -v
```

Where the current branch you are on is the branch you are comparing with, and `develop` is the base branch. In this example, `phpcs-diff` would run the following diff statement:

```shell
git diff my-current-branch develop
```

## Installation

The recommended method of installing this library is via [Composer](https://getcomposer.org/).

### Composer

Run the following command from your project root:

    composer global require olivertappin/phpcs-diff

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

## Requirements

`phpcs-diff` requires PHP version 5.6.0 or later. This project also depends on `phpcs` which is used internally to fetch the failed violations.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for information.
