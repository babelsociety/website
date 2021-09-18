<?php
namespace BabelSociety;

class Response {
    /** @var int **/
    private $statusCode;

    /** @var array **/
    private $data;

    function __construct(
        int $statusCode,
        array $data = []
    ) {
        $this->statusCode = $statusCode;
        $this->data  = $data;
    }

    public function getCode(): int {
        return $this->statusCode;
    }

    public function getData(): array {
        return $this->data;
    }

    public function hasData(): bool {
        return !empty($this->data);
    }

    public static function created(): Response {
        return new Response(HttpStatus::CREATED);
    }

    public static function methodNotAllowed(): Response {
        return new Response(HttpStatus::METHOD_NOT_ALLOWED);
    }

    public static function notAcceptable(): Response {
        return new Response(HttpStatus::NOT_ACCEPTABLE);
    }

    public static function error(string $cause): Response {
        return new Response(HttpStatus::VALIDATION_ERROR, ['error' => $cause]);
    }
}
