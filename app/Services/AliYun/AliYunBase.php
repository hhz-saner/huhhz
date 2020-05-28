<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/4/15
 * Time: 2:56 PM
 */

namespace App\Services\AliYun;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

abstract class AliYunBase
{
    public $client;

    /**
     * AliYunBase constructor.
     */
    public function __construct()
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            if ($request->getMethod() == 'POST') {
                $params = [];
                parse_str($request->getBody()->getContents(), $params);
            } else {
                $params = \GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery());
            }

            $params['Version'] = $this->getVersion();
            $params['Format'] = 'JSON';
            $params['AccessKeyId'] = $this->getAccessKeyId();
            $params['SignatureMethod'] = $this->getSignatureMethod();
            $params['Timestamp'] = gmdate($this->getDateTimeFormat());
            $params['SignatureVersion'] = $this->getSignatureVersion();
            $params['SignatureNonce'] = uniqid();

            if (!empty($this->getRegionId())) {//有些接口需要区域ID
                $params['RegionId'] = $this->getRegionId();
            }
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
            'verify' => false,
            'http_errors' => false,
            'connect_timeout' => 3,
            'read_timeout' => 10,
            'debug' => false,
        ]);
    }

    abstract protected function getVersion();

    protected function getAccessKeyId()
    {
        return config('services.aliyun.accessKeyId');
    }

    protected function getSignatureMethod()
    {
        return 'HMAC-SHA1';
    }

    protected function getDateTimeFormat()
    {
        return 'Y-m-d\TH:i:s\Z';
    }

    protected function getSignatureVersion()
    {
        return '1.0';
    }

    protected function getRegionId()
    {
        return '';
    }

    public function getSignature(RequestInterface $request, array $params)
    {
        //参数排序
        ksort($params);
        $query = http_build_query($params, null, '&', PHP_QUERY_RFC3986);
        $source = $request->getMethod() . '&%2F&' . $this->percentEncode($query);
        return base64_encode(hash_hmac('sha1', $source, $this->getAccessSecret() . '&', true));
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    protected function getAccessSecret()
    {
        return config('services.aliyun.accessSecret');
    }
}
