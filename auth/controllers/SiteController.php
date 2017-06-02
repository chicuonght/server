<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-31
 * Time: 12:08 AM
 */

namespace auth\controllers;


use yii\base\ErrorException;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;

class SiteController extends \yii\rest\Controller
{
    public $serializer = [
        'class' => 'tuyakhov\jsonapi\Serializer',
        'pluralize' => true,  // makes {"type": "user"}, instead of {"type": "users"}
    ];

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/vnd.api+json' => Response::FORMAT_JSON,
                ],
            ]
        ]);
    }


    public function actionError()
    {

        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {

            $data = [
                'message' => $exception->getMessage()

            ];
            return $data;
        }


    }
}