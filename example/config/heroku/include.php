<?php
/**
 * The main application config
 */

namespace neam\bootstrap;

Config::expect("MAIL_SENDER_NAME", $default = "Foo Example App", $required = false);
Config::expect("MAIL_SENDER_EMAIL", $default = "hello@example.com", $required = false);
Config::expect("DATABASE_URL", $default = null, $required = true);
Config::expect("SMTP_URL", $default = null, $required = true);
Config::expect("SENTRY_DSN", $default = null, $required = true);
Config::expect("GA_TRACKING_ID", $default = null, $required = true);
