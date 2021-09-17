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

    public static function error(string $cause): Response {
        return new Response(HttpStatus::VALIDATION_ERROR, ['error' => $cause]);
    }
}
