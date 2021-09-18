<?php
namespace BabelSociety\Result;

/**
 * OkResult<Nothing, Ok>
 */
class OkResult implements Result {
    /** @var Ok */
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function then(callable $f): Result {
        return $f($this->value);
    }

    public function chain(callable $f): Result {
        $error = $f($this->value);

        return $error === null ? $this : new ErrResult($error);
    }

    public function foldOk(callable $f) {
        return $f($this->value);
    }
}
