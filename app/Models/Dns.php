<?php

namespace App\Models;

use App\Services\AliYun\DnsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class Dns extends Model
{
    public $dnsService;
    protected $primaryKey = 'RecordId';

    /**
     * Dns constructor.
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->dnsService = new DnsService('huhhz.me');
        parent::__construct($attributes);
    }

    public static function with($relations)
    {
        return new static;
    }

    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $perPage = Request::get('per_page', 10);

        $page = Request::get('page', 1);


        $res = $this->dnsService->getList($page, $perPage);
        $total = $res['TotalCount'];

        $data = $res['DomainRecords']['Record'];

        extract($data);

        $data = static::hydrate($data);

        $paginator = new LengthAwarePaginator($data, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public function find($id, $columns = ['*'])
    {
        $data = $this->dnsService->find($id);
        return $this->newFromBuilder($data);

    }

    // 获取单项数据展示在form中
    public static function findOrFail($id)
    {
        $class = (new static);
        $data = $class->dnsService->find($id);
        return$class->newFromBuilder($data);
    }


// 保存提交的form数据
    public function save(array $options = [])
    {
        $attributes = $this->getAttributes();

        if (isset($attributes['RecordId'])) {
            $changeStatus = Request::get('Status');
            if ($changeStatus) {
                $changeStatus = $changeStatus == 'on' ? true : false;
            } else {
                $changeStatus = $attributes['Status'] == 'ENABLE' ? true : false;
            }
            $this->dnsService->edit($attributes['RecordId'], $attributes['RR'], $attributes['Type'], $attributes['Value'], $changeStatus);
        } else {
            $this->dnsService->add($attributes['RR'], $attributes['Type'], $attributes['Value']);
        }
    }

    public function delete()
    {
        $id = $this->getKey();

        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }
        $this->dnsService->delete($id);
        $this->fireModelEvent('deleted', false);
        return true;
    }
}
