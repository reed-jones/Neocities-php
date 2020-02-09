# Neocities PHP Client Library
Neocities-php is a PHP wrapper of the [Neocities.org](https://neocities.org/) API. Now with [Jigsaw](https://jigsaw.tighten.co/) integration

## Installation
```sh
composer require reed-jones/neocities
```

## Usage
```php
// First we include the library
use ReedJones\Neocities\Neocities;

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

## Jigsaw Integration

Neocities-PHP can be integrated with [Tighten's Jigsaw](https://jigsaw.tighten.co/). After installing this packing into your jigsaw project, Register the plugin in `bootstrap.php`
```php
// bootstrap.php
Jigsaw::mixin(new \ReedJones\Neocities\NeocitiesDeployment($container));
```

With this in place, all that is left to do is add your neocities api key your your .env file (if id doesn't exist, create it)
```sh
# .env
NEO_CITIES_API_KEY="YOUR_API_KEY_HERE"
```

Now to build & deploy from the command line, run the new `jigsaw deploy` command which accepts the same parameters as the `build` command.
```sh
# Build & deploy your 'local' build
./vendor/bin/jigsaw deploy
```
```sh
# Build & deploy your 'production' build
./vendor/bin/jigsaw deploy production
```

### Jigsaw Programmatic Usage

After following the above instructions for setting up a neocities deployment with jigsaw, a `deployToNeocities` method is available for programmatic use from bootstrap.php (or elsewhere). An Example:
```php
// Programmatic API Usage
$events->afterBuild(function (Jigsaw $jigsaw) {
    if ($jigsaw->getEnvironment() === 'production') {
        // Automatic deployment after all production builds
        $jigsaw->deployToNeocities();
    }
});
```
