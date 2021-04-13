<?php


namespace d3yii2\d3activity\logic;


use foroco\BrowserDetection;
use Yii;
use yii\helpers\ArrayHelper;

class BrowserDataHelper
{
    public static function data(): array
    {
        $Browser = new BrowserDetection();
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $data = ArrayHelper::filter(
            $Browser->getAll($useragent), [
//                'os_type',
//                'os_family',
//                'os_name',
//                'os_version',
            'os_title',
            'device_type',
//                'browser_name',
//                'browser_version',
            'browser_title'
        ]);
        $data['userIp'] = Yii::$app->request->getUserIP();
        return $data;
    }
}