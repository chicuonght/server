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
                $this->setBadRequest();
                return $authorize->getErrors();
            }

            return $authorize->getUser();

        }

        /**
         * Get all Features and Permissions for create/update Role
         * @return Document
         */
        public function actionIndex()
        {
            Authorize::checkPermission('managePermission');

            $permission = new Permission();
            $roleName = Yii::$app->request->get('role');

            return $permission->getFeaturesAndPermissions($roleName);
        }


        public function actionByUser(int $userId){
            Authorize::checkPermission('read');
            $permission = new Permission();

            return $permission->getPermissionsByUser($userId);
        }

        public function actionByRole(string $roleName){
            Authorize::checkPermission('viewRole');
            $permission = new Permission();

            return $permission->getPermissionsByRole($roleName);
        }

    }