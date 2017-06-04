<?php

    namespace auth\controllers;

    use auth\models\Authorize;
    use auth\models\CreateRole;
    use auth\models\Role;
    use auth\models\RoleForm;
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

        /**
         * Get all roles
         * @return $this
         */
        public function actionIndex()
        {
            Authorize::checkPermission('manageRole');
            $role = new Role();

            return $role->getRoles();
        }

        /**
         * Create a role with its permissions
         * @return $this|array
         */
        public function actionCreate()
        {
            Authorize::checkPermission('createRole');

            $model = new RoleForm(['scenario' => RoleForm::SCENARIO_CREATE]);
            $model->load(\Yii::$app->request->post());
            if (!$model->validate()) {
                $this->setBadRequest();
                return $model->getErrors();
            }


            $data = $model->create();
            $this->setStatusCreated();
            return $data;

        }


        /**
         * Update Role with permissions
         * @return $this|array
         */
        public function actionUpdate()
        {


            $model = new RoleForm(['scenario' => RoleForm::SCENARIO_UPDATE]);
            $model->load(\Yii::$app->request->post());
            if (!$model->validate()) {
                $this->setBadRequest();
                return $model->getErrors();
            }

            Authorize::checkPermission('updateRole', $model->getRole()->toArray());

            $data = $model->update();
            $this->setStatusUpdated();
            return $data;
        }


        /**
         * Delete Role
         * @return $this|array
         */
        public function actionDelete(string $roleName)
        {
            $model = new Role(['roleName' => $roleName]);
            if (!$model->validate()) {
                $this->setBadRequest();
                return $model->getErrors();
            }

            Authorize::checkPermission('deleteRole', $model->getRole()->toArray());
            $model->deleteRole();
            $this->setStatusDeleted();

        }


        /**
         * Assign Role to user
         * @param string $roleName
         * @param int $userId
         * @return array|\yii\rbac\Assignment
         */
        public function actionAssign(string $roleName, int $userId)
        {
            $model = new Role(['roleName' => $roleName, 'userId' => $userId, 'scenario' => Role::SCENARIO_ASSIGN]);
            if (!$model->validate()) {
                $this->setBadRequest();
                return $model->getErrors();
            }

            Authorize::checkPermission('assignRole', $model->getRole()->toArray());
            $this->setStatusCreated();
            return $model->assignUser($userId);
        }


        /**
         * Revoke Role from user
         * @param string $roleName
         * @param int $userId
         * @return array
         */
        public function actionUnassign(string $roleName, int $userId)
        {
            $model = new Role(['roleName' => $roleName, 'userId' => $userId]);
            if (!$model->validate()) {
                $this->setBadRequest();
                return $model->getErrors();
            }

            Authorize::checkPermission('unassignRole', $model->getRole()->toArray());
            $this->setStatusDeleted();
            $model->unassignUser($userId);
        }



    }
