<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        BASE_PATH . "/app/controllers/",
        BASE_PATH . "/app/models/",
    ]
)->register();

$loader->registerNamespaces(
    [
        "Node\Service" => BASE_PATH . "/app/services/",
    ]
)->register();