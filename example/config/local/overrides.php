<?php
/**
 * Overrides the main application config - used when running the code locally
 */

namespace neam\bootstrap;

// Don't require config for sentry error reporting not google analytics tracking when running locally

Config::expect("SENTRY_DSN", $default = null, $required = false);
Config::expect("GA_TRACKING_ID", $default = null, $required = false);

// Different local services ip based on running from inside docker container (identified by the simple code path "/code") or locally

if (substr(getcwd(), 0, 5) == "/code") {
    $_ENV["LOCAL_SERVICES_IP"] = "172.17.42.1";
} else {
    $_ENV["LOCAL_SERVICES_IP"] = "127.0.0.1";
}

// Database configuration
$_ENV["DATABASE_SCHEME"] = "mysql";
$_ENV["DATABASE_HOST"] = $_ENV["LOCAL_SERVICES_IP"];
$_ENV["DATABASE_PORT"] = "13306";
$_ENV["DATABASE_USER"] = "root";
$_ENV["DATABASE_PASSWORD"] = ""; // This is set in secrets.php
$_ENV["DATABASE_NAME"] = "db";

Config::expect("DATABASE_URL", $default = null, $required = false); // Don't require the _URL constant since we've now instead set the above

// Mailcatcher is used as local SMTP by default
$_ENV["MAILCATCHER_HOST"] = $_ENV["LOCAL_SERVICES_IP"];
$_ENV["MAILCATCHER_HTTP_PORT"] = "1080";
$_ENV["MAILCATCHER_SMTP_PORT"] = "1025";
$_ENV["SMTP_HOST"] = $_ENV["MAILCATCHER_HOST"];
$_ENV["SMTP_PORT"] = $_ENV["MAILCATCHER_SMTP_PORT"];

Config::expect("SMTP_URL", $default = null, $required = false); // Don't require the _URL constant since we've now instead set the above

// Note: To send real emails locally devs can use Google's SMTP server with their own accounts: https://www.digitalocean.com/community/articles/how-to-use-google-s-smtp-server
/*
$_ENV["MAIL_SENDER_NAME"] = "Mr. T";
$_ENV["MAIL_SENDER_EMAIL"] = "devemail@gmail.com";
$_ENV["SMTP_HOST"] = "smtp.gmail.com";
$_ENV["SMTP_USERNAME"] = "foo";
$_ENV["SMTP_PASSWORD"] = "bar";
$_ENV["SMTP_PORT"] = "587";
$_ENV["SMTP_ENCRYPTION"] = "tls";
 */
