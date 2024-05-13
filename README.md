# Laravel 对接 EPUSDT 支付

本人不提供免费/收费的 EPUSDT 的技术支持，本仓库 Issues 只接受代码 BUG 问题。 EPUSDT的问题请提交给原作者仓库 [assimon/epusdt](https://github.com/assimon/epusdt)

## 安装

```bash
composer require "xiaohuilam/laravel-epusdt-payment" -vvv
```

## 配置

配置文件 `.env`

```env
EPUSDT_URL=     #接口地址
EPUSDT_TOKEN=   #接口APIKEY
```

## 使用
```php
<?php

$notifyUrl = 'https://xxx.com/notify';
$res = app('epusdt')->createTransaction(
    '123', # 订单号
    number_format($pay_amount, 2, '.', ''),
    $notifyUrl
);

$address = $respEpusdt->token; # 需要支付给的地址
$usdtAmount = $respEpusdt->actual_amount; # 需要支付金额
```

## 回调
```php
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class YourController extends Controller
{
    public function epusdtCallback(Request $request)
    {
        app('epusdt')->notify($request, function (Request $request) {
            if ($request->input('status') != 2) {
                # 状态不是已支付
                return false;
            }

            // 这里写您的发货逻辑，发货成功请return true，否则return false
            // 更多回调参数请见 @see https://github.com/assimon/epusdt/blob/master/wiki/API.md#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0-1
            $txid = $request->input('block_transaction_id'); # 交易号
            $address = $request->input('token'); # 收款地址
            $cnyAmount = $request->input('amount'); # 收款金额，CNY
            $usdtAmount = $request->input('actual_amount'); # 实付金额，USDT
        });
    }
}
```

## LICENSE
[MIT LICENSE](LICENSE)