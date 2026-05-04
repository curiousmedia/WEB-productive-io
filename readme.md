# Harvest #
Simple library to pull data from [Productive.io](https://productive.io/) using [Guzzle.](http://docs.guzzlephp.org/)

## Example ##
```php
$api = new CuriousMedia\ProductiveIo(
	'123', //id
	'abc', //token
);
```

### Single page request ###
```php
$user = $api->get('projects')->result('data')
```

### Multiple page request ###
```php
$users = $api->get('projects')->all()->results('data')
```

### Options ###
See Guzzle [request options](http://docs.guzzlephp.org/en/stable/request-options.html)for available options.
```php
$users = $api->get('projects', ['page' => ['size' => 1]])->all()->results('data');
```