# Neocities PHP Client Library
Neocities-php is a PHP wrapper of the [Neocities.org](https://neocities.org/) API.

## Installation
```sh
composer require reed-jones/neocities
```

## Usage
```php
// First we include the library
use ReedJones/Neocities/Neocities;

// Then we log in using either username/password or api key

$neocities = new Neocities([
    'username' => 'YOUR_USERNAME',
    'password' => 'YOUR_PASSWORD'
]);

// or

$neocities = new Neocities([
    'apiKey' => 'YOUR_API_KEY'
]);
```

### Uploading Files

To upload files pass an array with the key being the desired upload name on the server, and the value being the path to the local file.

```php
$result = $neocities->upload([
    'hello.html' => './local.html',
    'about.html' => './AboutMe.html'
]);

var_dump($result);
```

### Deleting Files

To delete files from the server, simply pass an array of the files you wish to delete.

```php
$result = $neocities->delete([
    'hello.html',
    'about.html'
]);

var_dump($result);
```


### Listing All Files On Your Site

```php
$result = $neocities->list();

var_dump($result);
```

### Getting Information About Your Site

```php
$result = $neocities->info();

var_dump($result);
```

### Getting Your API Key

If you are logging in using your username/password, you can use this to retrieve your API key.

```php
$result = $neocities->key();

var_dump($result);
```
