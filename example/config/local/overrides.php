<?php
/**
 * Overrides the main application config - used when running the code locally
 */

namespace neam\bootstrap;

// Don't require config for sentry error reporting nor google analytics tracking when running locally

Config::expect("SENTRY_DSN", $default = null, $required = false);
Config::expect("GA_TRACKING_ID", $default = null, $required = false);

// Don't require the _URL constant since we're setting DATABASE_ constants in .env

Config::expect("DATABASE_URL", $default = null, $required = false);

// Don't require the _URL constant since we're setting SMTP_ constants in .env

Config::expect("SMTP_URL", $default = null, $required = false);
