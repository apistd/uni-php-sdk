<?php

namespace Uni\Common;

use Uni\Common\UniResponse;

class Uni {
  const NAME = 'uni-php-sdk';
  const VERSION = '0.0.7';
  const USER_AGENT = self::NAME . '/' . self::VERSION;

  public $endpoint;
  public $accessKeyId;
  public $signingAlgorithm;
  public $hmacAlgorithm;

  private $accessKeySecret;

  function __construct($config) {
    $this->endpoint = $config['endpoint'] ?? 'https://uni.apistd.com';
    $this->accessKeyId = $config['accessKeyId'];
    $this->accessKeySecret = $config['accessKeySecret'];
    $this->signingAlgorithm = $config['signingAlgorithm'] ?? 'hmac-sha256';
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
