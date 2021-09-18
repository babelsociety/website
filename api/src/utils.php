<?php

use BabelSociety\Result\ErrResult;
use BabelSociety\Result\OkResult;
use BabelSociety\Result\Result;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @param callable $make () -> Endpoint
 */
function executeEndpoint(callable $make): void {
    try {
        $response = $make()->invoke(
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
}

function config(string $key): array {
    static $config;

    if ($config === null)
        $config = include(__DIR__ . '/../config.php');

    return $config[$key];
}

function getDatabase(): PDO {
    static $pdo;

    if ($pdo === null) {
        $config = config('db'); 

        $pdo = new PDO(
            "mysql:host=$config[host];port=$config[port];dbname=$config[name];charset=utf8mb4",
            $config['user'], $config['pswd']
        );
    }

    return $pdo;
}

function logger(): Logger {
    static $logger;

    if ($logger !== null)
        return $logger;

    $formatter = new LineFormatter();
    $formatter->includeStacktraces();
    $formatter->ignoreEmptyContextAndExtra();

    $fname = __DIR__ . '/../logs/api-' . date('Y-m-d') .'.log';
    $handler = new StreamHandler($fname);
    $handler->setFormatter($formatter);

    $logger = new Logger('main');
    $logger->pushHandler($handler);

    return $logger;
}

/**
 * @param Err $error
 * @return Result<Err, null>
 */
function chain(bool $condition, $error): Result {
    return $condition ? new OkResult(null) : new ErrResult($error);
}

/**
 * @param Result<Err, Ok>[] $results
 * @param callable $f (Ok[]) => T
 * @return Result<Err, T>
 */
function resultAll(array $results, callable $f): Result {
    $parsed = [];

    foreach ($results as $k => $r) {
        if ($r instanceof ErrResult)
            return $r;

        if ($r instanceof OkResult) 
            $parsed[$k] = $r->getValue();
    }

    return new OkResult($f($parsed));
}
