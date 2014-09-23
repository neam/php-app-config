PHP App Config
=====================

A PHP framework-agnostic approach to make the current [config](http://12factor.net/config) available to PHP applications and shell-scripts.

For use by the application wherever it may be fit, for instance in the application's / PHP framework's configuration files.

## Installation

1. Install via composer:


    composer.phar require neam/php-app-config:dev-develop

2. Copy the `example/config` folder into the root directory of your project.


    cp -r vendor/neam/php-app-config/example/config config

3. Make sure that all app entry scripts include the file called `include.php` right after including the composer autoloader (adjust the path as necessary in each entry script):


    // Make app config available as PHP constants
    require(dirname(__FILE__) . '/vendor/neam/php-app-config/include.php');

4. Replace config vars in your app with the corresponding PHP constants. Example of usage within Yii configuration:

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
        'connectionString' => 'mysql:host=' . DATABASE_HOST . (defined('DATABASE_PORT') && !empty(DATABASE_PORT) ? ';port=' . DATABASE_PORT : '') . ';dbname=' . DATABASE_NAME,
        'emulatePrepare' => true,
        'username' => DATABASE_USER,
        'password' => DATABASE_PASSWORD,
        'charset' => 'utf8',
        'enableParamLogging' => true, // Log SQL parameters
        //'schemaCachingDuration'=>3600*24,
    ),

## How it works

Configuration directives are expected to be available through environment variables, either by the system, web server, php-fpm or by hackishly populating $_ENV during a bootstrapping phase (see the `local` example config file for the latter).

The `include.php` file will load a file (defaulting to `project-root/config/local/include.php`) that defines what application-specific config to expect.

To change the expected config include file, set the `CONFIG_INCLUDE` environment variable to the path to the include relative to the project root. For instance, in order to use `project-root/config/heroku/include.php`, set the CONFIG_INCLUDE environment variable to `config/heroku/include.php`.

Within the config include file, you define which directives to expect using the Config::expect() method:

    Config::expect("DATABASE_URL", $default = null, $required = true);

The `include.php` file will then call `Config::defineConstants()` which will define expected configuration directives as constants.

## Using the example config includes

The included example configuration includes sample config profiles used to deploy to Heroku and/or running the code locally.

### Heroku

1. Deploy the app to Heroku.
2. Set the CONFIG_INCLUDE config var to `heroku`

    $ heroku config:set CONFIG_INCLUDE=config/heroku/include.php

3. Set the expected config vars (DATABASE_URL, GA_TRACKING_ID etc) to their respective values

Your app should now run on Heroku using the expected config vars as PHP constants.

### Local

The example local config include will first expect the `heroku`, then load the `overrides.php` file, followed by a non-versioned file called `secrets.php`. To use the local configuration:

1. Create your secret local configuration file

    cp ../core/app/config/envbootstrap/local/envbootstrap.dist.php ../core/app/config/envbootstrap/local/envbootstrap.php

2. Add general defaults/overrides that all developers use locally in the `overrides.php` file
3. Add secret defaults/overrides that should not be versioned to the `secrets.php` file

Your app should now run locally using the expected config vars as PHP constants.

## Usage

To show your current config, you can run the following in a shell:

    php vendor/neam/php-app-config/export.php

To use the config in a shell-script:

    php vendor/neam/php-app-config/export.php | tee /tmp/php-app-config.sh
    source /tmp/php-app-config.sh
    # now the config is available to the shell-script

## Special configuration directives

Some times it may be more convenient to set access details using the URL format. The helper class currently includes special handling of the following configuration directives.

### DATABASE_URL or DATABASE_*-constants

Determines what backing service to use for MySQL-compatible database access.

The relevant constants are:

 * DATABASE_SCHEME
 * DATABASE_HOST
 * DATABASE_USER
 * DATABASE_PASSWORD
 * DATABASE_NAME

Database connection details can also be supplied by setting the DATABASE_URL configuration directive in the format `mysql2://username:urlencodedpassword@host:port/db`

When DATABASE_URL and DATABASE_*-constants both are available, the individual DATABASE_*-constants are ignored.

### SMTP_URL or SMTP_*-constants

Determines what backing service to use for mail sending.

The relevant constants are:

 * SMTP_HOST
 * SMTP_USERNAME
 * SMTP_PASSWORD
 * SMTP_PORT
 * SMTP_ENCRYPTION

SMTP details can also be supplied by setting the SMTP_URL configuration directive in the format `smtp://username:urlencodedpassword@host:587?encryption=tls`

When SMTP_URL and SMTP_*-constants both are available, the individual SMTP_*-constants are ignored.
