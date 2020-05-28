<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/4/16
 * Time: 3:22 PM
 */

namespace App\Services\QCloud;


class OcrService extends QCloudBase
{

    public $baseUri = 'https://ocr.tencentcloudapi.com/';


    public function getVersion()
    {
        return '2018-11-19';
    }

    public function ocr($image)
    {
        $res = $this->client->post($this->baseUri, [
            'json' => [
                'Action' => 'GeneralBasicOCR',
                $this->getImageKey($image) => $image
            ]
        ]);
        $return = '';
        $data = json_decode($res->getBody()->getContents(), true)['Response']['TextDetections'];
        foreach ($data as $value) {
            $return .= $value['DetectedText'];
            $return .= "\r\n";
        }
        return $return;
    }

    protected function getImageKey($image)
    {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return 'ImageUrl';
        }
        return 'ImageBase64';
    }

    public function fastOcr($image)
    {

        $res = $this->client->post($this->baseUri, [
            'json' => [
                'Action' => 'GeneralFastOCR',
                $this->getImageKey($image) => $image
            ]
        ]);
        $return = '';
        $data = json_decode($res->getBody()->getContents(), true);
        foreach ($data['Response']['TextDetections'] as $value) {
            $return .= $value['DetectedText'];
            $return .= "\r\n";
        }
        return $return;
    }
}
