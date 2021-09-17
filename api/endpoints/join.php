<?php
require(__DIR__ . '/../vendor/autoload.php');

use BabelSociety\Endpoint\JoinEndpoint;

try {
    $response = JoinEndpoint::create()->invoke(
        $_SERVER['REQUEST_METHOD'],
        file_get_contents('php://input')
    );

    header('Accept: application/json');
    http_response_code($response->getCode());

    if ($response->hasData()) {
        header('Content-Type: application/json');
        echo json_encode($response->getData());
    }
} 
catch (Throwable $t) {
    http_response_code(500);
    logger()->error($t);
}
