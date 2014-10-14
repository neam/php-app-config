<?php
// Expect the "paas" config as base

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'paas' . DIRECTORY_SEPARATOR . 'include.php');

// Add local overrides

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'overrides.php');

// An example hack - Different local services ip based on running from inside docker container (identified by the simple code path "/code") or locally

if (substr(getcwd(), 0, 5) == "/code") {
    $_ENV["LOCAL_SERVICES_IP"] = "172.17.42.1";
} else {
    $_ENV["LOCAL_SERVICES_IP"] = "127.0.0.1";
}

// Include the secrets file containing non-versioned secrets

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'secrets.php');
