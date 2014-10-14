<?php

namespace neam\bootstrap;

class Config
{

    static protected $expected = array();

    /**
     * Adds a config variable that is expected to be set through environment variables together
     *
     * @param $ref the configuration directive
     * @param null $default it's default value
     * @param bool $required whether or not it is required for the app to run
     */
    static public function expect($ref, $default = null, $required = false)
    {
        static::$expected[$ref] = compact("default", "required");
    }

    /**
     * Is a configuration directive non-empty?
     */
    static public function configured($ref)
    {
        $value = static::read($ref, $default = null, $required = false);
        return !empty($value);
    }

    /**
     * Handle special directives - optional - see README.md
     */
    static public function handleSpecialDirectives()
    {
        if (static::read("EXPAND_CONFIG_URLS") != "1") {
            return;
        }

        if (static::configured("DATABASE_URL")) {
            $url = parse_url(static::read("DATABASE_URL"));
            $_ENV["DATABASE_SCHEME"] = $url['scheme'];
            $_ENV["DATABASE_HOST"] = $url['host'];
            $_ENV["DATABASE_PORT"] = $url['port'];
            $_ENV["DATABASE_USER"] = $url['user'];
            $_ENV["DATABASE_PASSWORD"] = urldecode($url['pass']);
            $_ENV["DATABASE_NAME"] = trim($url['path'], '/');
        } else {
            unset(static::$expected["DATABASE_URL"]);
        }
        static::expect("DATABASE_SCHEME");
        static::expect("DATABASE_HOST");
        static::expect("DATABASE_PORT");
        static::expect("DATABASE_USER");
        static::expect("DATABASE_PASSWORD");
        static::expect("DATABASE_NAME");
        if (static::configured("SMTP_URL")) {
            $url = parse_url(static::read("SMTP_URL"));
            $_ENV["SMTP_HOST"] = $url['host'];
            $_ENV["SMTP_PORT"] = $url['port'];
            $_ENV["SMTP_USERNAME"] = $url['user'];
            $_ENV["SMTP_PASSWORD"] = urldecode($url['pass']);
            $_ENV["SMTP_ENCRYPTION"] = isset($args['encryption']) ? $args['encryption'] : false;
        } else {
            unset(static::$expected["SMTP_URL"]);
        }
        static::expect("SMTP_HOST");
        static::expect("SMTP_PORT");
        static::expect("SMTP_USERNAME");
        static::expect("SMTP_PASSWORD");
        static::expect("SMTP_ENCRYPTION");
    }

    /**
     * Make environment config available as immutable constants
     */
    static public function defineConstants()
    {
        // Handle special directives
        static::handleSpecialDirectives();

        // Set the corresponding PHP constants
        foreach (static::$expected as $ref => $var) {
            static::defineConstant($ref, $var["default"], $var["required"]);
        }
    }

    /**
     * Gets the value from the environment variables
     *
     * @param $ref
     * @param null $default returned when the value is empty
     * @param bool $required
     * @throws Exception in case a required value was requested but found empty
     */
    static public function read($ref, $default = null, $required = false)
    {
        // Start with $value as null
        $value = null;

        // Try $_ENV first
        if (isset($_ENV[$ref])) {
            $value = $_ENV[$ref];
        }

        // Fallback to using getenv
        if (empty($value) && function_exists('getenv')) {
            $value = getenv($ref);
        }

        // Fallback to using apache_getenv
        if (empty($value) && function_exists('apache_getenv')) {
            $value = apache_getenv($ref);
        }

        // Fallback to $_SERVER
        if (empty($value) && isset($_SERVER[$ref])) {
            $value = $_SERVER[$ref];
        }

        // Handle the case when the env var is empty
        if (empty($value)) {
            if ($required) {
                throw new \Exception("Environment variable $ref needs to be non-empty. Adjust app configuration and re-build.");
            }
            // All configuration directives are expected to be non-empty, but since this one is not required, we settle with writing
            // a warning to the error_log
            error_log("$ref empty, defaulting to $default");
            $value = $default;
        }

        return $value;
    }

    /**
     * Sets a PHP constant based on an environment variable's corresponding PHP value
     *
     * @param $ref
     * @param null $default
     * @param bool $required
     * @throws Exception
     */
    static public function defineConstant($ref, $default, $required)
    {
        // Get the value from the environment variables
        $value = static::read($ref, $default, $required);

        // Treat the strings "true" and "false" as booleans
        if ($value === "true") {
            $value = true;
        }
        if ($value === "false") {
            $value = false;
        }

        // Set the corresponding PHP constant
        define($ref, $value);
    }

    /**
     * Exports the current config
     */
    static public function exportValues()
    {
        // Handle special directives
        static::handleSpecialDirectives();

        // Export the current config
        $values = array();
        foreach (static::$expected as $ref => $var) {
            $values[$ref] = static::read($ref, $var["default"], $var["required"]);
        }

        return $values;
    }

    /**
     * Exports the current config
     */
    static public function export($format = "shell")
    {
        $output = "";
        $values = static::exportValues();
        switch ($format) {
            default:
            case "shell":

                foreach ($values as $ref => $val) {
                    $output .= escapeshellcmd("export $ref=\"$val\"") . "\n";
                }

                break;
        }

        return $output;
    }

}
