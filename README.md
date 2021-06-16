# Uni PHP SDK

[UniSMS](https://unisms.apistd.com/) - 高可用聚合短信服务平台官方 PHP SDK.

## 文档

查看完整产品介绍与 API 文档请访问 [UniSMS Documentation](https://unisms.apistd.com/docs).

## 安装

Uni PHP SDK 使用 Packagist 托管，可从公共 [Packagist 仓库](https://packagist.org/packages/apistd/uni-sdk) 中获得。

使用 Composer 在项目中添加 `apistd/uni-sdk` 作为依赖：

```bash
composer require apistd/uni-sdk
```

## 使用示例

以下示例展示如何使用 Uni PHP SDK 快速调用服务。

### 发送短信

```php

use Uni\Common\UniException;
use Uni\SMS\UniSMS;

// 初始化
$client = new UniSMS([
  'accessKeyId' => 'your access key id',
  'accessKeySecret' => 'your access key secret'
]);

// 发送短信
try {
  $resp = $client->send([
    'to' => 'your phone number',
    'signature' => 'UniSMS',
    'templateId' => 'login_tmpl',
    'templateData' => [
      'code' => 7777
    ]
  ]);
  var_dump($resp->data);
} catch (UniException $e) {
  print_r($e);
}

```

## 相关参考

### 其他语言 SDK

- [Java](https://github.com/apistd/uni-java-sdk)
- [Go](https://github.com/apistd/uni-go-sdk)
- [Node.js](https://github.com/apistd/unisms-node-sdk)
- [Python](https://github.com/apistd/uni-python-sdk)
