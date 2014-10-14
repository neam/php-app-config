<?php

namespace neam\bootstrap;

$extension_root = dirname(__FILE__);
$project_root = $extension_root . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';

// Make the helper classes available without requiring autoloading
if (!class_exists('Config', false)) {
    require($extension_root . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Config.php');
}
if (!class_exists('\Dotenv', false)) {
    require($project_root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'vlucas' . DIRECTORY_SEPARATOR . 'phpdotenv' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Dotenv.php');
}

// Define what application-specific config to expect - see README.md for more information
$config_include = Config::read('CONFIG_INCLUDE', $default = 'config/local/include.php', $required = false);
$config = $project_root . DIRECTORY_SEPARATOR . $config_include;
if (!is_readable($config)) {
    echo "PHP app config file not available ($config).";
    die(2);
}
require($config);
