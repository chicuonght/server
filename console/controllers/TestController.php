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
        $results = \Yii::$app->authManager->getRoles();
        VarDumper::dump($results);
    }

    public function actionApi()
    {


        $client = new Client();
        $host = 'rbac.dev';
        $url = sprintf('http://%s/role', $host);

        $params = [
            'data' => [
                "type" => "CreateRole",
                "attributes" => [
                    'roleName' => 'user',
                    'description' => 'a new user role',
                    'permissions' => [
                        'viewPost',
                        'updatePost'
                    ],
                ],
            ],
        ];


        $headers = [
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJpYXQiOjE0OTY0NzA2ODcsImV4cCI6MTQ5NjU1NzA4NywibmFtZSI6IkFkbWluIFdNUyIsInVzZXJuYW1lIjoiY2l0dHBoIiwianRpIjoxfQ.VtW8D9FVqDHv0iGY_1ruDmiZqVbSKheXZJ651ntD9Mk',
            'Content-Type' => 'application/vnd.api+json'
        ];
        $request = $client->createRequest()
            ->setMethod('POST')
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