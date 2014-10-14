<?php

namespace neam\bootstrap;

// Make the helper classes available without requiring autoloading
if (!class_exists('Config', false)) {
    require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Config.php');
}
if (!class_exists('Dotenv', false)) {
    require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor/vlucas/phpdotenv/src/Dotenv.php');
}

// Define what application-specific config to expect - see README.md for more information
$config_include = Config::read('CONFIG_INCLUDE', $default = 'config/local/include.php', $required = false);
$project_root = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
$config = $project_root . DIRECTORY_SEPARATOR . $config_include;
if (!is_readable($config)) {
    echo "PHP app config file not available ($config).";
    die(2);
}
require($config);
