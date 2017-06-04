<?php
    /**
     * Created by PhpStorm.
     * User: thanh
     * Date: 2017-05-30
     * Time: 4:33 PM
     */

    namespace auth\controllers;

    use auth\models\User;
    use yii\filters\auth\HttpBearerAuth;
    use yii\filters\ContentNegotiator;
    use yii\helpers\ArrayHelper;
    use yii\helpers\VarDumper;
    use yii\web\Response;

    class BearerAuthController extends \yii\rest\Controller
    {
        public $serializer = [
            'class'     => 'tuyakhov\jsonapi\Serializer',
            'pluralize' => false,  // makes {"type": "user"}, instead of {"type": "users"}
        ];

        public function behaviors()
        {
            return ArrayHelper::merge(parent::behaviors(), [
                'bearerAuth'        => [
                    'class' => HttpBearerAuth::className(),
                ],
                'contentNegotiator' => [
                    'class'   => ContentNegotiator::className(),
                    'formats' => [
                        'application/vnd.api+json' => Response::FORMAT_JSON,
                    ],
                ],
            ]);
        }

        public function setBadRequest()
        {
            \Yii::$app->response->setStatusCode(400);
        }

        public function setStatusCreated()
        {
            \Yii::$app->response->setStatusCode(201);
        }

        public function setStatusUpdated()
        {
            \Yii::$app->response->setStatusCode(201);
        }

        public function setStatusDeleted()
        {
            \Yii::$app->response->setStatusCode(204);
        }

    }