# Dozuki API PHP Client

### Description

This is a PHP client library for accessing the Dozuki V2.0 API (https://www.dozuki.com/api/2.0/doc/)

### Requirements

PHP 5.3 or higher

Guzzle (https://github.com/guzzle/guzzle)

Tests depend on PHPUnit (http://www.phpunit.de/manual/current/en/installation.html)

## Usage

```php
require_once 'vendor/autoload.php';

use WhyteSpyder\DozukiPHPClient\DozukiClient;

$client = DozukiClient::factory(
	array(
    	'dozuki_domain'     => 'example.dozuki.com'    // required
	)
);

$command = $client->getCommand(
	'category',
	array(
    	'categoryname' => 'Test'
	)
);
$results = $command->execute(); // returns an array of results
```

You can find a list of the client's available commands in the bundle's
[dozuki_config.json](https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/src/dozuki_config.json) but basically
they should be the same as the [api endpoints listed in the docs](https://www.dozuki.com/api/2.0/doc/).