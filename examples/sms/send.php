<?php

require_once './src/Uni/common.php';
require_once './src/Uni/sms.php';

use Uni\Common\UniException;
use Uni\SMS\UniSMS;

$client = new UniSMS([
  'accessKeyId' => 'your access key id',
  'accessKeySecret' => 'your access key secret'
]);

try {
  $resp = $client->send([
    'to' => 'your phone number',
    'signature' => 'UniSMS',
    'templateId' => 'login_tmpl',
    'templateData' => [
      'code' => 8888
    ]
  ]);
} catch (UniException $e) {
  print_r($e);
}

var_dump($resp->data);
