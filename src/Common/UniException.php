<?php

namespace Uni\Common;

class UniException extends \Exception {
  public $code;
  public $requestId;

  function __construct($code = '', $message = '', $requestId = '') {
    parent::__construct($message, 0);
    $this->code = $code;
    $this->requestId = $requestId;
  }

  function __toString() {
    return '['.__CLASS__.'] code: '.$this->code.', message: '.$this->getMessage().', requestId: '.$this->requestId;
  }
}
