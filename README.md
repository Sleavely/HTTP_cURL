# HTTP_cURL #

I wrote this class to save myself some headaches typing `$ch` in every `curl_*`-method.

## Basic usage ##

```php
require_once('class.http_curl.php');
```

The general principle is set around HTTP methods, because most APIs nowadays are RESTful (and boy, don't we love it!).

So far it supports:
- GET
- POST
- DELETE

Although the idea is to support the less used methods, like `$http->PATCH()`,  
this might take a while because I add to the class as I go along.

### HTTP GET ###

```php
$http = new HTTP_cURL('http://example.com/');
$response = $http->GET();
```

Slightly more advanced usage:

```php
// The class can be instantiated with or without a target URL
$http = new HTTP_cURL();
// Logic to determine location here
$target_url = (rand(0,1) === 1 ? 'http://example.com/0' : 'http://example.com/1')

// All cURL options can be set like this.
// http://php.net/manual/en/function.curl-setopt.php
$http->{CURLOPT_URL} = $target_url;

// ..but for changing the URL there's a shortcut
$http->setUrl($target_url);

// Off you go!
$response = $http->GET();

// Debugging?
print_r($http->last_request);
```

### HTTP POST ###

```php
$http = new HTTP_cURL('http://example.com/');
$postdata = array(
  'name' => 'John Doe',
	'email' => 'example@example.com'
);
$response = $http->POST($postdata);
```

`POST()` takes a second argument as well:
```
@param bool $multipart
 	Whether to use multipart/form-data or not.
 	Defaults to application/x-www-form-urlencoded
```
