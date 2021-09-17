<?php
namespace BabelSociety\Result;

/**
 * Result<Err, Ok>
 */
interface Result {
    /**
     * @param $f  (Ok) => Result<Err, B>
     * @return Result<Err, B>
     */
    public function then(callable $f): Result;

    /**
     * @param $f (Ok) => ?Err
     * @return Result<Err, Ok>
     */
    public function chain(callbale $f): Result;

    /**
     * This function expects a Result<T, Ok> and fold the Ok value into a T
     *
     * @param $f (Ok) => T
     * @return T
     */
    public function foldOk(callable $f);

}
