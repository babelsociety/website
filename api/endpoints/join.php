<?php
require(__DIR__ . '/../vendor/autoload.php');

use BabelSociety\Endpoint\JoinEndpoint;

executeEndpoint(function() {
    return JoinEndpoint::create();
});
