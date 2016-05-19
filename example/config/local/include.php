<?php
// Expect the "remote" config as base

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'remote' . DIRECTORY_SEPARATOR . 'include.php');

// Add local overrides

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'overrides.php');

// An example hack - Different local services ip based on running from inside docker container (identified by the simple code path "/code") or locally

if (substr(getcwd(), 0, 5) == "/code") {
    $_ENV["LOCAL_SERVICES_IP"] = "172.17.42.1";
} else {
    $_ENV["LOCAL_SERVICES_IP"] = "127.0.0.1";
}

// Loads sensitive (non-versioned) environment variables from .env to getenv(), $_ENV.

\Dotenv::makeMutable();
\Dotenv::load($project_root);
