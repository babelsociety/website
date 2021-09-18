<?php
namespace BabelSociety\Result;

/**
 * ErrResult<Err, Nothing>
 */
class ErrResult implements Result {
    /** @var Ok */
    private $error;

    public function __construct($error) {
        $this->error = $error;
    }

    public function getError() {
        return $this->error;
    }

    public function then(callable $f): Result {
        return $this;
    }

    public function chain(callable $f): Result {
        return $this;
    }

    public function foldOk(callable $f) {
        return $this->error;
    }
}
