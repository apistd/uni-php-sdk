<?php

namespace Uni\Common;

use Uni\Common\UniException;

class UniResponse {
  const REQUEST_ID_HEADER_KEY = 'x-uni-request-id';

  public $headers;
  public $code;
  public $data;
  public $raw;
  public $requestId;

  function __construct($resp) {
    list($raw_headers, $raw_body) = explode("\r\n\r\n", $resp, 2);
    $this->headers = $this->parse_headers($raw_headers);
    $this->requestId = $this->headers[self::REQUEST_ID_HEADER_KEY];

    $data = json_decode($raw_body);
    $code = $data->code;

    if ($code != 0) {
      throw new UniException($data->message, $code, $this->requestId);
    }

    $this->code = $code;
    $this->data = $data->data;
    $this->raw = $resp;
  }

  private function parse_headers($raw_headers) {
    $headers = [];

    foreach (explode("\n", $raw_headers) as $i => $h) {
      $h = explode(':', $h, 2);

      if (isset($h[1])) {
        if(!isset($headers[$h[0]])) {
          $headers[$h[0]] = trim($h[1]);
        } else if(is_array($headers[$h[0]])) {
          $tmp = array_merge($headers[$h[0]],array(trim($h[1])));
          $headers[$h[0]] = $tmp;
        } else {
          $tmp = array_merge(array($headers[$h[0]]),array(trim($h[1])));
          $headers[$h[0]] = $tmp;
        }
      }
    }

    return $headers;
  }
}
