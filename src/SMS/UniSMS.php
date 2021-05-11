<?php

namespace Uni\SMS;

use Uni\Common\Uni;

class UniSMS {
  function __construct($config) {
    $this->client = new Uni($config);
  }

  function send($params) {
    return $this->client->request('sms.message.send', $params);
  }
}
