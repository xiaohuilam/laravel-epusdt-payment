<?php

namespace Xiaohuilam\LaravelEpusdtPayment;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Epusdt
{
    protected $baseUrl;
    protected $signKey;

    public function __construct($url, $signKey)
    {
        $this->baseUrl = $url;
        $this->signKey = $signKey;
    }

    protected function http()
    {
        return Http::asJson();
    }

    protected function sign(array $parameter, string $signKey)
    {
        ksort($parameter);
        reset($parameter);
        $sign = '';
        $urls = '';
        foreach ($parameter as $key => $val) {
            if ($val == '') continue;
            if ($key != 'signature') {
                if ($sign != '') {
                    $sign .= "&";
                    $urls .= "&";
                }
                $sign .= "$key=$val";
                $urls .= "$key=" . urlencode($val);
            }
        }
        $sign = md5($sign . $signKey);
        return $sign;
    }

    protected function makeCall($url, $parameter)
    {
        $parameter['signature'] = $this->sign($parameter, $this->signKey);   
        $res = $this->http()->post($this->baseUrl . $url, $parameter)->object();

        if (200 !== $res->status_code) {
            throw new \Exception($res->message);
        }
        return $res->data;
    }

    /**
     * 创建支付订单
     * @param string $orderNumber 订单号
     * @param float $amount 付款金额
     * @param string $notifyUrl 回调地址
     * @param string|null $currency 币种
     */
    public function createTransaction($orderNumber, $amount, $notifyUrl, $currency = null)
    {
        $parameter = [
            "order_id" => $orderNumber,
            "amount" => (float) $amount,
            "notify_url" => $notifyUrl,
            "redirect_url" => $notifyUrl,
        ];
        if ($currency) {
            $parameter['currency'] = $currency;
        }
        return $this->makeCall('/api/v1/order/create-transaction', $parameter);
    }

    /**
     * 支付成功后回调通知
     * @param Request $request
     * @param callable $callback
     */
    public function notify(Request $request, $callback)
    {
        $assert_signature = $this->sign($request->all(), $this->signKey);
        $real_signature = $request->input('signature');
        if ($assert_signature != $real_signature) {
            throw new HttpResponseException(response('签名错误')->bypassStructure());
        }
        if (!$callback($request)) {
            throw new HttpResponseException(response('处理失败')->bypassStructure());
        }
        throw new HttpResponseException(response('ok')->bypassStructure());
    }
}
