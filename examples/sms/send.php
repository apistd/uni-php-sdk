<?php

require_once './src/Common/Uni.php';
require_once './src/Common/UniException.php';
require_once './src/Common/UniResponse.php';
require_once './src/SMS/UniSMS.php';

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
  var_dump($resp->data);
} catch (UniException $e) {
  print_r($e);
}
