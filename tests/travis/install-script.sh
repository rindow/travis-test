#!/bin/sh

composer install

if [ "$TRAVIS_PHP_VERSION" -eq "7.2" ]; then
	composer require --dev "phpunit/phpunit 6.*"
fi
