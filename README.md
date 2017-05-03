# phergie/phergie-irc-plugin-react-bridge

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for providing the ability to send and receive IRC messages via middleware.

[![Build Status](https://secure.travis-ci.org/phergie/phergie-irc-plugin-react-bridge.png?branch=master)](http://travis-ci.org/phergie/phergie-irc-plugin-react-bridge)

## Install

The recommended method of installation is [through composer](http://getcomposer.org).
Be advised you may need to set `minimum-stability` to `dev` to use this plugin.
[Read about minimum-stability here](https://getcomposer.org/doc/04-schema.md#minimum-stability)

```
composer require phergie/phergie-irc-plugin-react-bridge:dev-master
```

:exclamation: This plugin requires `Phergie Irc Bot React` version less than 2
and greater than 1.0. These are old versions of the bot core and this plugin
will probably require modifications to work with newer versions of the bot.

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
