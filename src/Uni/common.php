<?php

namespace Uni\Common;

class UniException extends \Exception {
  protected $requestId;
  protected $code;

  function __construct($code = '', $message = '',  $requestId = '') {
    parent::__construct($message, 0);
    $this->code = $code;
    $this->requestId = $requestId;
  }

  function __toString() {
    return '['.__CLASS__.'] code: '.$this->code.', message: '.$this->getMessage().', requestId: '.$this->requestId;
  }
}

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

class Uni {
  const NAME = 'uni-php-sdk';
  const VERSION = '0.0.1';
  const USER_AGENT = self::NAME . '/' . self::VERSION;

  function __construct($config) {
    $this->endpoint = $config['endpoint'] ?: 'https://uni.apistd.com';
    $this->accessKeyId = $config['accessKeyId'];
    $this->accessKeySecret = $config['accessKeySecret'];
    $this->signingAlgorithm = $config['signingAlgorithm'] ?: 'hmac-sha256';
    $this->hmacAlgorithm = explode('-', $this->signingAlgorithm)[1];
  }

  private function sign($query) {
    if (isset($this->accessKeySecret)) {
      $query['algorithm'] = $this->signingAlgorithm;
      $query['timestamp'] = time();
      $query['nonce'] = bin2hex(random_bytes(12));

      ksort($query);
      $strToSign = http_build_query($query);

      $query['signature'] = hash_hmac($this->hmacAlgorithm, $strToSign, $this->accessKeySecret);
    }

    return $query;
  }

  function request($action, $data) {
    $curl = curl_init();
    $query = [
      'action' => $action,
      'accessKeyId' => $this->accessKeyId
    ];
    $query = $this->sign($query);
    $body_str = json_encode($data);

    curl_setopt_array($curl, [
      CURLOPT_URL => $this->endpoint . '/?' . http_build_query($query),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => [
        'User-Agent: '. self::USER_AGENT,
        'Content-Type: '. 'application/json;charset=utf-8'
      ],
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $body_str
    ]);

    $response = curl_exec($curl);

    curl_close($curl);
    return new UniResponse($response);
  }
}
