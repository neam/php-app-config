PHP App Config
=====================

A PHP framework-agnostic approach to make the current [config](http://12factor.net/config) available to PHP applications and shell-scripts.

For use by the application wherever it may be fit, for instance in the application's / PHP framework's configuration files.

## Installation

- Install via composer:

```
composer.phar require neam/php-app-config:dev-develop
```

- Copy the `example/config` folder into the root directory of your project.

```
cp -r vendor/neam/php-app-config/example/config config
```

- Make sure that all app entry scripts include the file called `include.php` right after including the composer autoloader (adjust the path as necessary in each entry script):

```
// Make app config available as PHP constants
require(dirname(__FILE__) . '/../vendor/neam/php-app-config/include.php');
```

- Replace config vars in your app with the corresponding PHP constants. Example of usage within Yii configuration:

Before:

    'db' => array(
        'connectionString' => 'mysql:host=localhost;dbname=db',
        'emulatePrepare' => true,
        'username' => 'root',
        'password' => 'supersecret',
        'charset' => 'utf8',
        'enableParamLogging' => true, // Log SQL parameters
        //'schemaCachingDuration'=>3600*24,
    ),

After:

    'db' => array(
        'connectionString' => 'mysql:host=' . DATABASE_HOST . (defined('DATABASE_PORT') && DATABASE_PORT != '' ? ';port=' . DATABASE_PORT : '') . ';dbname=' . DATABASE_NAME,
        'emulatePrepare' => true,
        'username' => DATABASE_USER,
        'password' => DATABASE_PASSWORD,
        'charset' => 'utf8',
        'enableParamLogging' => true, // Log SQL parameters
        //'schemaCachingDuration'=>3600*24,
    ),

## How it works

Configuration directives are looked for in the following places:
 * $_ENV (which can be populated during a bootstrapping phase as in the `local` example config file)
 * php-fpm environment variables (set in php-fpm configuration files and available through getenv())
 * apache sub-process environment variables (set in Apache 2.x vhost config and available through apache_getenv())
 * php-cgi params (set in nginx location blocks using fastcgi_param and available through $_SERVER)

The `include.php` file will load a file (defaulting to `project-root/config/local/include.php`) that defines what application-specific configuration directives to expect.

To change the expected config include file, set the `CONFIG_INCLUDE` environment variable to the path to the include relative to the project root. For instance, in order to use `project-root/config/paas/include.php`, set the CONFIG_INCLUDE environment variable to `config/paas/include.php`.

Within the config include file, you define which directives to expect using the Config::expect() method:

    Config::expect("DATABASE_URL", $default = null, $required = true);

The `include.php` file will then call `Config::defineConstants()` which will define expected configuration directives as constants.

## Using the example config includes

The included example configuration includes sample config profiles used to deploy to Heroku-style services and/or running the code locally.

### Platform as a Service

- Deploy the app to a PaaS, in this example we use Heroku - adapt accordingly for other PaaS providers.
- Set the CONFIG_INCLUDE config var to `config/paas/include.php`

```
$ heroku config:set CONFIG_INCLUDE=config/paas/include.php
```

- Set the expected config vars (DATABASE_URL, GA_TRACKING_ID etc) to their respective values

Your app should now run on Heroku using the expected config vars as PHP constants.

### Local

The example local config include will first expect the `paas`, then load the `overrides.php` file, followed by a non-versioned file called `secrets.php`. To use the local configuration:

- Create ang gitignore your secret local configuration file

```
cp .env.dist .env
echo ".env" >> .gitignore
```

- Add general defaults/overrides that all developers use locally in the `overrides.php` file
- Add secret defaults/overrides that should not be versioned to the `secrets.php` file

Your app should now run locally using the expected config vars as PHP constants.

## Useful commands

To show your current config, you can run the following in a shell:

    php vendor/neam/php-app-config/export.php

To use the config in a shell-script:

    php vendor/neam/php-app-config/export.php | tee /tmp/php-app-config.sh
    source /tmp/php-app-config.sh
    # now the config is available to the shell-script
