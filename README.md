MonologExtension
================

- [master](https://github.com/swestcott/MonologExtension) [![Build Status](https://travis-ci.org/swestcott/MonologExtension.png?branch=master)](https://travis-ci.org/swestcott/MonologExtension)
- [develop](https://github.com/swestcott/MonologExtension/tree/develop) [![Build Status](https://travis-ci.org/swestcott/MonologExtension.png?branch=develop)](https://travis-ci.org/swestcott/MonologExtension)

Integrates the [Monolog](https://github.com/Seldaek/monolog) logging framework with Behat.

Example Usage
-------------

behat.yml,

``` 
default:
  extensions:
    swestcott\MonologExtension\Extension:
      handlers:
        stdout:
          type: stream
          path: php://stdout
          level: debug
```

Context/Sub-Context,

```php
class class FeatureContext extends BehatContext
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

Installation
------------

Just add the dependency to composer.json,

```json
    "require": {
        ...
        "swestcott/monolog-extension": "*"
    }
```

Copyright
---------

Copyright (c) 2013 Simon Westcott. See LICENSE for details.
