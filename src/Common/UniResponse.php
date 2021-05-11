<?php

namespace Uni\Common;

use Uni\Common\UniException;

class UniResponse {
  const REQUEST_ID_HEADER_KEY = 'x-uni-request-id';

  function __construct($resp) {
    list($raw_headers, $raw_body) = explode("\r\n\r\n", $resp, 2);
    $this->headers = $this->parse_headers($raw_headers);
    $this->requestId = $this->headers[self::REQUEST_ID_HEADER_KEY];

    $data = json_decode($raw_body);
    $code = $data->code;

    if ($code != 0) {
      throw new UniException($code, $data->message, $this->requestId);
    }

    $this->code = $code;
    $this->data = $data->data;
    $this->raw = $resp;
  }

  private function parse_headers($raw_headers) {
    $headers = [];
    $key = '';

    foreach(explode("\n", $raw_headers) as $i => $h) {
      $h = explode(':', $h, 2);

      if (isset($h[1])) {
          if (!isset($headers[$h[0]]))
            $headers[$h[0]] = trim($h[1]);
          elseif (is_array($headers[$h[0]])) {
            $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
          } else {
            $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
          }
          $key = $h[0];
      } else {
        if (substr($h[0], 0, 1) == "\t")
          $headers[$key] .= "\r\n\t".trim($h[0]);
        elseif (!$key)
          $headers[0] = trim($h[0]);trim($h[0]);
      }
    }

    return $headers;
  }
}
