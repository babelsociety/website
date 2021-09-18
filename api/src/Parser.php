<?php
namespace BabelSociety;

use BabelSociety\Result\Result;
use BabelSociety\Result\ErrResult;
use BabelSociety\Result\OkResult;

class Parser {
    /**
     * @return Result<Response, array>
     */
    public function parseJson(string $raw): Result {
        $json = json_decode($raw, true);

        return ($json === null)
            ? new ErrResult(Response::notAcceptable())
            : new OkResult($json); 
    }

    /**
     * @return Result<Response, string>
     */
    public function reqField(string $fieldName, array $source, string $field): Result {
        return empty($source[$field])
            ? $this->error($fieldName, 'please provide a value for this field')
            : $this->noBlanks($fieldName, $source[$field]);
    }

    /**
     * @return Result<Response, ?string>
     */
    public function optField(string $fieldName, array $source, string $field): Result {
        return empty($source[$field])
            ? new OkResult(null)
            : $this->noBlanks($fieldName, $source[$field]);
    }

    /**
     * @return Result<Response, bool>
     */
    public function optFieldBool(string $fieldName, array $source, string $field): Result {
        return new OkResult(
            empty($source[$field]) ? false : boolval($source[$field])
        );
    }

    /**
     * @return Result<Response, string>
     */
    public function email(string $email): Result {
        return filter_var($email, FILTER_VALIDATE_EMAIL)
            ? new OkResult($email)
            : $this->error('E-Mail', 'please provide a valid address');
    }

    public function noBlanks(string $fieldName, string $raw): Result {
        $trimmed = trim($raw);

        return ($raw === $trimmed)
            ? new OkResult($trimmed)
            : $this->error($fieldName, 'please remove all white spaces at the start or at the end of this field');
    }

    private function error(string $fieldName, string $cause): Result {
        return new ErrResult(
            Response::error($fieldName . ': ' . $cause)
        );
    }
}
