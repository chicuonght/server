<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-30
 * Time: 11:48 AM
 */

namespace console\controllers;


use Codeception\Util\HttpCode;
use Symfony\Component\Console\Helper\HelperSet;
use yii\console\Controller;
use yii\helpers\Url;
use yii\helpers\VarDumper;

use yii\httpclient\Client;
use yii\web\UrlManager;


class TestController extends Controller
{

    public function actionRun()
    {
        $results = \Yii::$app->authManager->getPermissionsByUser(1);
        VarDumper::dump($results);
    }

    public function actionApi()
    {


        $client = new Client();
        $host = 'rbac.dev';
        $url = sprintf('http://%s/permissions', $host);

        $params = [
            'data' => [
                "type" => "Authorize",
                "attributes" => [
                    'permission' => 'manageRole',
                    'data' => [
                        'created_by' => 1
                    ],
                ],
            ],
        ];


        $headers = [
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJpYXQiOjE0OTYyMjYwMTksImV4cCI6MTQ5NjMxMjQxOSwibmFtZSI6IlRoYW5oIFBoYW0iLCJ1c2VybmFtZSI6ImNpdHRwaCIsImp0aSI6MX0.WIBDt9L40oDx6yXkcrsZrp0c8O8zBaDVkwNeQtN_N5E',
            'Content-Type' => 'application/vnd.api+json'
        ];
        $request = $client->createRequest()
            ->setMethod('GET')
            ->setHeaders($headers)
            ->setUrl($url)
            ->setContent(json_encode($params));
        $url = $request->getUrl();

        $response = $request->send();

        $jsonString = ($response->getContent());
        $data = json_decode($jsonString);
        $data = $data ? $data : $jsonString;
        if ($response->isOk) {
            VarDumper::dump($data);
        } else {

            VarDumper::dump($data);
            die('fail');
        }
    }

}