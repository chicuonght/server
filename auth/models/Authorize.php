<?php
    /**
     * Created by PhpStorm.
     * User: thanh
     * Date: 2017-05-31
     * Time: 8:23 PM
     */

    namespace auth\models;

    use tuyakhov\jsonapi\ResourceIdentifierInterface;
    use tuyakhov\jsonapi\ResourceInterface;
    use tuyakhov\jsonapi\ResourceTrait;
    use yii\base\Model;
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
                $this->addError($attribute, 'Params must be array');
            }
        }

        public static function checkPermission($permissionName, $params = [])
        {
            if (!\Yii::$app->user->can($permissionName, $params)) {
                $msg = sprintf("Sorry! You don't have permission: '%s'", $permissionName);
                throw new UnauthorizedHttpException($msg);
            }
        }

        public function getUser()
        {
            self::checkPermission($this->permission, $this->params);

            return User::$_instance;
        }


    }