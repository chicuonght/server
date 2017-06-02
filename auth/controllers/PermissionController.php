<?php
    /**
     * Created by PhpStorm.
     * User: thanh
     * Date: 2017-05-31
     * Time: 2:19 PM
     */

    namespace auth\controllers;

    use auth\models\Authorize;
    use auth\models\Permission;
    use JsonApiPhp\JsonApi\Document\Document;
    use JsonApiPhp\JsonApi\Document\Resource\ResourceObject;
    use yii;

    class PermissionController extends BearerAuthController
    {
        /**
         * Authorize user and data
         * @return Document
         * @throws yii\web\UnauthorizedHttpException
         */
        public function actionAuthorize()
        {
            $request = Yii::$app->request;

            $authorize = new Authorize();
            $authorize->load(\Yii::$app->request->post());
            if (!$authorize->validate()) {
                $error = new yii\base\DynamicModel([
                    'message' => 'Invalid Params',
                    'data'    => $authorize->getErrors(),
                ]);
                $this->setBadRequest();

                return $authorize->getErrors();
            }

            return $authorize->getUser();

        }

        /**
         * Get all Features and Permissions
         * @return Document
         */
        public function actionIndex()
        {
            Authorize::checkPermission('managePermission');

            $permission = new Permission();
            $roleName = Yii::$app->request->get('role');

            return $permission->getPermissions($roleName);
        }

    }