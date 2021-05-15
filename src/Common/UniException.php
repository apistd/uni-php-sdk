<?php

namespace Uni\Common;

class UniException extends \Exception {
  public $code;
  public $requestId;

  function __construct($message = '', $code = '', $requestId = '') {
    parent::__construct($message, 0);
    $this->code = $code;
    $this->requestId = $requestId;
  }

  function __toString() {
    return '['.__CLASS__.'] ['.$this->code.'] '.$this->getMessage().', requestId: '.$this->requestId;
  }
}
