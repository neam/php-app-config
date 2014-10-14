<?php
/**
 * The main application config
 */

namespace neam\bootstrap;

// ==== Identity-related config ====

Config::expect("MAIL_SENDER_NAME", $default = "Foo Example App", $required = false);
Config::expect("MAIL_SENDER_EMAIL", $default = "hello@example.com", $required = false);

// ==== Infrastructure-related config ====

// Since we define these as URLs
$_ENV["EXPAND_CONFIG_URLS"] = "1";

// Support setting main db constants based on DATABASE_URL environment variable
Config::expect("DATABASE_URL", $default = null, $required = false);

// Require setting smtp constants based on SMTP_URL environment variable
Config::expect("SMTP_URL", $default = null, $required = true); // smtp://username:password@host:587?encryption=tls

// Sentry error reporting
Config::expect("SENTRY_DSN", $default = null, $required = true);

// Google Analytics ID
Config::expect("GA_TRACKING_ID", $default = null, $required = true);

// ==== Debug-related config ====

Config::expect("YII_DEBUG", $default = false);
