# phergie/phergie-irc-plugin-react-bridge

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for providing the ability to send and receive IRC messages via middleware.

[![Build Status](https://secure.travis-ci.org/phergie/phergie-irc-plugin-react-bridge.png?branch=master)](http://travis-ci.org/phergie/phergie-irc-plugin-react-bridge)

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

```
composer require phergie/phergie-irc-plugin-react-bridge:dev-master
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
new \Phergie\Irc\Plugin\React\Bridge\Plugin(array(



))
```

## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
cd tests
../vendor/bin/phpunit
```

## License

Released under the BSD License. See `LICENSE`.
