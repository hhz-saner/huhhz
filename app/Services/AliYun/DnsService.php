<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/4/15
 * Time: 3:47 PM
 */

namespace App\Services\AliYun;


class DnsService extends AliYunBase
{
    public static $typeMap = [
        'A' => 'A-将域名指向一个IPV4地址',
        'CNAME' => 'CNAME-将域名指向另外一个域名',
        'AAAA' => 'AAAA-将域名指向一个IPV6地址',
        'NS' => 'NS-将子域名指定其他DNS服务器解析',
        'MX' => 'MX-将域名指向邮件服务器地址',
        'SRV' => 'SRV-记录提供特定的服务的服务器',
        'TXT' => 'TXT-文本长度限制512,通常做SPF记录(反垃圾邮件)',
        'CAA' => 'CAA-CA证书颁发机构授权校验'
    ];
    public $domainName = 'huhhz.me';
    public $baseUri = 'http://alidns.aliyuncs.com/';
    public $count;


    /**
     * DnsService constructor.
     * @param string $domainName
     */
    public function __construct(string $domainName)
    {
        $this->domainName = $domainName;
        parent::__construct();
    }

    public function getList($page, $perPage)
    {
        $client = $this->client;
        $res = $client->get($this->baseUri . '?' . http_build_query([
                'Action' => 'DescribeDomainRecords',
                'DomainName' => $this->domainName,
                'PageNumber' => $page,
                'PageSize' => $perPage,
            ]));
        return json_decode($res->getBody()->getContents(), true);
    }

    public function find($recordId)
    {
        $client = $this->client;
        $res = $client->get($this->baseUri . '?' . http_build_query([
                'Action' => 'DescribeDomainRecordInfo',
                'RecordId' => $recordId,
            ]));
        return json_decode($res->getBody()->getContents(), true);
    }

    public function add($rr, $type, $value)
    {
        if (!$this->verifyType($type)) {
            return false;
        }
        $client = $this->client;
        $client->get($this->baseUri . '?' . http_build_query([
                'Action' => 'AddDomainRecord',
                'DomainName' => $this->domainName,
                'RR' => $rr,
                'Type' => $type,
                'Value' => $value,
            ]));
    }

    protected function verifyType($type)
    {
        return collect(self::$typeMap)->has($type);
    }

    public function edit($recordId, $rr, $type, $value, bool $status = true)
    {
        if (!$this->verifyType($type)) {
            return false;
        }
        $client = $this->client;
        $client->get($this->baseUri . '?' . http_build_query([
                'Action' => 'UpdateDomainRecord',
                'RecordId' => $recordId,
                'RR' => $rr,
                'Type' => $type,
                'Value' => $value,
            ]));
        $client->get($this->baseUri . '?' . http_build_query([
                'Action' => 'SetDomainRecordStatus',
                'RecordId' => $recordId,
                'Status' => $status ? 'Enable' : 'Disable',
            ]));
    }

    public function delete($recordId)
    {
        $client = $this->client;
        $client->get($this->baseUri . '?' . http_build_query([
                'Action' => 'DeleteDomainRecord',
                'RecordId' => $recordId,
            ]));
    }

    protected function getVersion()
    {
        return '2015-01-09';
    }
}
