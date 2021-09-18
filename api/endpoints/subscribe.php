<?php
require(__DIR__ . '/../vendor/autoload.php');

use BabelSociety\Endpoint\SubscribeEndpoint;

executeEndpoint(function() {
    return SubscribeEndpoint::create();
});
