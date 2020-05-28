<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/4/16
 * Time: 11:20 AM
 */

namespace App\Services\QCloud;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

abstract class QCloudBase
{
    public $client;

    public function __construct()
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            if ($request->getMethod() == 'POST') {
                $params = json_decode($request->getBody()->getContents(), true);
            } else {
                $params = \GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery());
            }
            $params['Region'] = $this->getRegion();
            $params['Timestamp'] = time();
            $params['Nonce'] = random_int(1, 9999);
            $params['SecretId'] = $this->getAccessKeyId();
            $params['SignatureMethod'] = $this->getSignatureMethod();
            $params['Version'] = $this->getVersion();

            //签名
            $params['Signature'] = $this->getSignature($request, $params);
            $body = http_build_query($params, '', '&');
            if ($request->getMethod() == 'POST') {
                $request = \GuzzleHttp\Psr7\modify_request($request, ['body' => $body]);
            } else {
                $request = \GuzzleHttp\Psr7\modify_request($request, ['query' => $body]);
            }
            return $request;
        }));
        $this->client = new Client([
            'handler' => $stack,
//            'verify' => false,
//            'http_errors' => false,
//            'connect_timeout' => 3,
//            'read_timeout' => 10,
//            'debug' => false,
        ]);
    }

    protected function getRegion()
    {
        return 'ap-beijing';
    }

    protected function getAccessKeyId()
    {
        return config('services.qcloud.accessKeyId');
    }

    protected function getSignatureMethod()
    {
        return 'HmacSHA1';
    }

    abstract protected function getVersion();

    public function getSignature(RequestInterface $request, array $params)
    {

        $uri = $request->getUri();

        $srcStr = $request->getMethod() . $uri->getHost() . $uri->getPath() . $this->buildParamStr($params, $request->getMethod());
        dd($request->getMethod(),$uri->getHost(), $uri->getPath() , $this->buildParamStr($params, $request->getMethod()));
        $secret = $this->getAccessSecret();
        $method = $this->getSignatureMethod();

        switch ($method) {
            case 'HmacSHA1':
                return base64_encode(hash_hmac('sha1', $srcStr, $secret, true));
                break;
            case 'HmacSHA256':
                return base64_encode(hash_hmac('sha256', $srcStr, $secret, true));
                break;
        }
        return '';
    }

    protected function buildParamStr($requestParams, $requestMethod = 'POST')
    {
        $paramStr = '';
        ksort($requestParams);
        $i = 0;
        foreach ($requestParams as $key => $value) {
            if ($key == 'Signature') {
                continue;
            }
            // 排除上传文件的参数
            if ($requestMethod == 'POST' && substr($value, 0, 1) == '@') {
                continue;
            }
            // 把 参数中的 _ 替换成 .
            if (strpos($key, '_')) {
                $key = str_replace('_', '.', $key);
            }
            if ($i == 0) {
                $paramStr .= '?';
            } else {
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . $value;
            $i++;
        }
        return $paramStr;
    }

    protected function getAccessSecret()
    {
        return config('services.qcloud.accessSecret');
    }

    protected function getSignatureVersion()
    {
        return '1.0';
    }
}
