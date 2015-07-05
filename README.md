MonologExtension
================

- [master](https://github.com/swestcott/MonologExtension) [![Build Status](https://travis-ci.org/swestcott/MonologExtension.png?branch=master)](https://travis-ci.org/swestcott/MonologExtension) [![Coverage Status](https://coveralls.io/repos/swestcott/MonologExtension/badge.svg?branch=master)](https://coveralls.io/r/swestcott/MonologExtension?branch=master)
- [develop](https://github.com/swestcott/MonologExtension/tree/develop) [![Build Status](https://travis-ci.org/swestcott/MonologExtension.png?branch=develop)](https://travis-ci.org/swestcott/MonologExtension) [![Coverage Status](https://coveralls.io/repos/swestcott/MonologExtension/badge.svg?branch=develop)](https://coveralls.io/r/swestcott/MonologExtension?branch=develop)

Integrates the [Monolog](https://github.com/Seldaek/monolog) logging framework with Behat.

Installation
------------

Add the dependency to composer.json,

```json
"require": {
    ...
    "swestcott/monolog-extension": "*"
}
```

And install/update your dependancies,

```bash
$ curl http://getcomposer.org/installer | php
$ php composer.phar install
```

Configuration
-------------

```yaml
# behat.yml
default:
  extensions:
    swestcott\MonologExtension\Extension:
      handlers:
        stdout:
          type: stream
          path: php://stdout
          level: debug
```

Usage
-----

Each context/subcontext is assigned it's own Monolog channel, named after context class name. It is set directly against the context.

### Example 1

```php
class FeatureContext extends BehatContext
{
    /**
     * @When /^I add together "([^"]*)" and "([^"]*)"$/
     */
    public function iAddTogether($value1, $value2)
    {
        $this->logger->info('Adding "' . $value1 . '" and "' . $value2 . '"');
        $this->result = $value1 + $value2;
    }
}
```

Output,

```
[2013-01-01 00:00:00] FeatureContext.INFO: Adding "1" and "2" [] []
```

### Example 2

Including context in messages

```php
class FeatureContext extends BehatContext
{
    /**
     * @When /^I add together "([^"]*)" and "([^"]*)"$/
     */
    public function iAddTogether($value1, $value2)
    {
        $this->logger->info('Adding values', array($value1, $value2));
        $this->result = $value1 + $value2;
    }
}
```

Output,

```
[2013-01-01 00:00:00] FeatureContext.INFO: Adding values ["1", "2"] []
```

Copyright
---------

Copyright (c) 2013 Simon Westcott. See LICENSE for details.
