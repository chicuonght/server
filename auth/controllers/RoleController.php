<?php

    namespace auth\controllers;

    use auth\models\Authorize;
    use auth\models\Role;
    use JsonApiPhp\JsonApi\Document\Document;
    use JsonApiPhp\JsonApi\Document\Resource\ResourceObject;
    use Yii;
    use yii\filters\VerbFilter;
    use yii\filters\AccessControl;
    use common\models\LoginForm;
    use yii\helpers\VarDumper;
    use yii\rest\Controller;

    /**
     * Site controllerrol
     */
    class RoleController extends BearerAuthController
    {

        public function actionIndex()
        {
            Authorize::checkPermission('manageRole');
            $role = new Role();

            return $role->getRoles();
        }

        public function actionCreate(){

        }

        public function actionUpdate(){

        }
    }
