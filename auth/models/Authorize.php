<?php
    /**
     * Created by PhpStorm.
     * User: thanh
     * Date: 2017-05-31
     * Time: 8:23 PM
     */

    namespace auth\models;

    use auth\rbac\DbManager;
    use yii\base\Model;
    use yii\web\BadRequestHttpException;
    use yii\web\UnauthorizedHttpException;

    class Authorize extends Model
    {


        public $permission;

        public $params = [];

        public function rules()
        {
            return [
                ['permission', 'required'],
                ['permission', 'string', 'max' => 64],
                ['params', 'validateParams'],
            ];
        }


        public function validateParams($attribute, $params, $validator)
        {
            if ($this->$attribute && !is_array($this->$attribute)) {
                $this->addError($attribute, 'Params must be in array');
            }
        }

        /**
         * @param $permissionName
         * @param array $params
         * @return bool
         * @throws BadRequestHttpException
         * @throws UnauthorizedHttpException
         */
        public static function checkPermission($permissionName, $params = [])
        {
            if(\Yii::$app->getUser()->getId() == DbManager::SUPPER_ADMIN_ID){
                return true;
            }

            if (!\Yii::$app->user->can($permissionName, $params)) {
                $permission = \Yii::$app->authManager->getPermission($permissionName);

                if(is_null($permission)){
                    $msg = sprintf("Permission '%s' not found!", $permissionName);
                    throw new BadRequestHttpException($msg);
                }

                $msg = sprintf("Sorry! You don't have permission: %s", $permissionName);
                throw new UnauthorizedHttpException($msg);
            }
            return true;
        }

        public function getUser()
        {
            self::checkPermission($this->permission, $this->params);

            return User::$_instance;
        }


    }