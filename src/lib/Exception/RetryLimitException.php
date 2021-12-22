<?php

namespace Fostam\Retry\Exception;

use Exception;
use Throwable;

class RetryLimitException extends Exception {
    private $tries;

    public function __construct($tries, Throwable $previous = null) {
        $this->tries = $tries;
        parent::__construct("execution failed {$tries} times", 0, $previous);
    }

    public function getTries() {
        return $this->tries;
    }
}